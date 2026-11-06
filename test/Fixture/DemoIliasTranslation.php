<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use Base3\Translation\Api\ITranslation;

final class DemoIliasTranslation implements ITranslation {

	public array $requests = [];

	public function translate(string $set, string $section, string $key, string $fallback = '', array $replacements = []): string {
		$this->requests[] = [$set, $section, $key];
		return $section . ':' . $key;
	}
}
