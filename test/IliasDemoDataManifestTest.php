<?php declare(strict_types=1);

namespace IliasDemoData\Test;

use IliasDemoData\IliasDemoDataPlugin;
use IliasDemoData\SettingsTab\IliasdemodatalifecycleSettingsTab;
use PHPUnit\Framework\TestCase;

final class IliasDemoDataManifestTest extends TestCase {

	public function testManifestMakesPluginDiscoverableWithoutSpecialHostRegistration(): void {
		$file = dirname(__DIR__) . '/base3.json';
		self::assertFileExists($file);
		$manifest = json_decode((string)file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
		self::assertSame(1, $manifest['manifestVersion']);
		self::assertSame('IliasDemoData', $manifest['namespace']);
		self::assertNotEmpty($manifest['name']);
		self::assertNotEmpty($manifest['version']);
		self::assertSame('iliasdemodata', IliasDemoDataPlugin::getName());
	}

	public function testSettingsFeatureTextsAreOwnedByThePlugin(): void {
		$path = dirname(__DIR__) . '/lang/Settings/en.ini';
		self::assertFileExists($path);
		$translations = parse_ini_file($path, true, INI_SCANNER_RAW);
		self::assertIsArray($translations);
		$name = IliasdemodatalifecycleSettingsTab::getName();
		self::assertArrayHasKey($name, $translations);
		self::assertNotEmpty($translations[$name]['label']);
		self::assertNotEmpty($translations[$name]['description']);
	}

	public function testDisplayIncludesOwnInvalidRequestTranslation(): void {
		$path = dirname(__DIR__) . '/lang/Display/en.ini';
		self::assertFileExists($path);
		$translations = parse_ini_file($path, true, INI_SCANNER_RAW);
		self::assertIsArray($translations);
		self::assertNotEmpty($translations['ilias_demo_data_lifecycle_test_display']['invalid_request']);
	}
}
