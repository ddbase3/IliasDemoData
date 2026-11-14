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

use Base3\CoreFoundation\Api\IMvcView;

final class DemoIliasView implements IMvcView {

	public array $_ = [];
	public string $lastPath = '';
	public string $template = '';
	public array $loads = [];

	public function setPath(string $path = '.') { $this->lastPath = $path; }
	public function assign(string $key, $value) { $this->_[$key] = $value; }
	public function setTemplate(string $template = 'default') { $this->template = $template; }
	public function loadTemplate(): string {
		$this->loads[] = $this->template;
		$path = dirname(__DIR__, 2) . '/tpl/' . $this->template;
		ob_start();
		try { require $path; return (string)ob_get_clean(); }
		catch (\Throwable $e) { ob_end_clean(); throw $e; }
	}
	public function loadBricks(string $set, string $language = '') { $this->loads[] = 'bricks:' . $set; }
	public function getBricks(string $set): ?array {
		return $set === 'ilias_demo_data_lifecycle_test_display'
			? (parse_ini_file(dirname(__DIR__, 2) . '/lang/Display/en.ini', true)[$set] ?? [])
			: null;
	}
}
