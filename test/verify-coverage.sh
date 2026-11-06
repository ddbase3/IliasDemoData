#!/bin/sh
set -eu

ROOT=$(CDPATH= cd -- "$(dirname -- "$0")/../../../.." && pwd)
cd "$ROOT"

PHPUNIT_BIN=${PHPUNIT_BIN:-vendor/composer/vendor/bin/phpunit}
if [ ! -f "$PHPUNIT_BIN" ]; then
	echo "PHPUnit not found: $PHPUNIT_BIN" >&2
	exit 2
fi

REPORT=$(mktemp "${TMPDIR:-/tmp}/iliasdemodata-coverage.XXXXXXXX.xml")
trap 'rm -f "$REPORT"' EXIT HUP INT TERM

XDEBUG_MODE=coverage php "$PHPUNIT_BIN" \
	--no-configuration \
	--bootstrap vendor/composer/vendor/autoload.php \
	--coverage-filter components/Base3/IliasDemoData/src \
	--coverage-clover "$REPORT" \
	components/Base3/IliasDemoData/test

if [ ! -s "$REPORT" ]; then
	echo 'A PHPUnit coverage driver (PCOV or Xdebug) is required.' >&2
	exit 1
fi

php -r '
$xml = simplexml_load_file($argv[1]);
if ($xml === false || !isset($xml->project->metrics)) {
	fwrite(STDERR, "Unable to read the Clover project coverage metrics.\n");
	exit(1);
}
$metrics = $xml->project->metrics;
$total = (int)$metrics["statements"];
$covered = (int)$metrics["coveredstatements"];
if ($total < 1) {
	fwrite(STDERR, "No production statements were measured.\n");
	exit(1);
}
printf("IliasDemoData statement coverage: %d/%d (%.2f%%)\n", $covered, $total, $covered / $total * 100);
if ($covered !== $total) {
	fwrite(STDERR, "The requested 100%% statement coverage has not been reached.\n");
	exit(1);
}
' "$REPORT"
