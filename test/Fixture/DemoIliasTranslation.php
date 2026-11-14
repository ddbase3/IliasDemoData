<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of IliasDemoData for BASE3 Framework.
 *
 * IliasDemoData manages ILIAS demonstration content lifecycles.
 * It owns controlled seed content and related administration views.
 * Native ILIAS repository and authorization boundaries are preserved.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0-or-later
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/


namespace Base3\IliasDemoData\Test\Fixture;

use Base3\CoreFoundation\Translation\Api\ITranslation;

final class DemoIliasTranslation implements ITranslation {

	public array $requests = [];

	public function translate(string $set, string $section, string $key, string $fallback = '', array $replacements = []): string {
		$this->requests[] = [$set, $section, $key];
		return $section . ':' . $key;
	}
}
