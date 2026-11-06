<?php declare(strict_types=1);

namespace IliasDemoData\Test\SettingsTab;

use Base3\Authorization\Api\IFeature;
use IliasDemoData\Display\IliasDemoDataLifecycleTestDisplay;
use IliasDemoData\SettingsTab\IliasdemodatalifecycleSettingsTab;
use IliasDemoData\Test\Fixture\DemoIliasAuthorization;
use IliasDemoData\Test\Fixture\DemoIliasTranslation;
use PHPUnit\Framework\TestCase;
use UiFoundation\Api\ISettingsTab;

final class IliasdemodatalifecycleSettingsTabTest extends TestCase {

	protected function setUp(): void {
		require_once dirname(__DIR__) . '/Fixture/DemoIliasAuthorization.php';
		require_once dirname(__DIR__) . '/Fixture/DemoIliasTranslation.php';
	}

	public function testDiscoverableTechnicalIdentityAndParentGroup(): void {
		$tab = new IliasdemodatalifecycleSettingsTab(new DemoIliasTranslation(), new DemoIliasAuthorization());
		self::assertInstanceOf(ISettingsTab::class, $tab);
		self::assertInstanceOf(IFeature::class, $tab);
		self::assertSame('iliasdemodatalifecyclesettingstab', IliasdemodatalifecycleSettingsTab::getName());
		self::assertSame('base3settingstab', $tab->getParentTabName());
		self::assertSame(IliasDemoDataLifecycleTestDisplay::getName(), $tab->getDisplayName());
		self::assertIsInt($tab->getOrder());
		self::assertNull($tab->getDisplayData());
	}

	public function testTabUsesPluginOwnedSettingsTranslation(): void {
		$translation = new DemoIliasTranslation();
		$tab = new IliasdemodatalifecycleSettingsTab($translation, new DemoIliasAuthorization());
		self::assertSame(IliasdemodatalifecycleSettingsTab::getName() . ':label', $tab->getLabel());
		self::assertSame(IliasdemodatalifecycleSettingsTab::getName() . ':description', $tab->getDescription());
		self::assertSame([
			['Settings', IliasdemodatalifecycleSettingsTab::getName(), 'label'],
			['Settings', IliasdemodatalifecycleSettingsTab::getName(), 'description'],
		], $translation->requests);
	}

	public function testFeatureVisibilityRequiresNativeAdminAndFeatureGrants(): void {
		foreach ([
			[true, true, true], [false, true, false],
			[true, false, false], [false, false, false]
		] as [$native, $feature, $expected]) {
			$authorization = new DemoIliasAuthorization($native, $feature);
			$tab = new IliasdemodatalifecycleSettingsTab(new DemoIliasTranslation(), $authorization);
			self::assertSame($expected, $tab->isVisible());
		}
	}

	public function testFeaturePermissionMatchesTechnicalName(): void {
		$tab = new IliasdemodatalifecycleSettingsTab(new DemoIliasTranslation(), new DemoIliasAuthorization());
		self::assertNotEmpty($tab->getPermissions());
		self::assertSame(IliasdemodatalifecycleSettingsTab::getName(), $tab->getPermissions()[0]->permission);
		self::assertIsArray($tab->getDefaultAudience());
	}
}
