<?php declare(strict_types=1);

namespace IliasDemoData\Test;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;
use IliasDemoData\Api\IDemoDataLifecycleService;
use IliasDemoData\IliasDemoDataPlugin;
use IliasDemoData\Service\IliasDemoDataLifecycleService;
use IliasDemoData\Test\Fixture\DemoIliasContainerSpy;
use IliasDemoData\Test\Fixture\DemoIliasState;
use ILIAS\DI\Container;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class IliasDemoDataPluginTest extends TestCase {

	public function testPluginHasUniqueIdentityAndImplementsFrameworkContract(): void {
		self::assertSame('iliasdemodata', IliasDemoDataPlugin::getName());
		self::assertTrue(is_subclass_of(IliasDemoDataPlugin::class, IPlugin::class));
	}

	public function testInitRegistersLazySharedLifecycleServiceWithoutInstantiation(): void {
		require_once __DIR__ . '/Fixture/DemoIliasState.php';
		require_once __DIR__ . '/Fixture/DemoIliasContainerSpy.php';
		DemoIliasState::install();
		$dic = new Container();
		$container = new DemoIliasContainerSpy([Container::class => $dic]);
		$plugin = new IliasDemoDataPlugin($container);
		$plugin->init();

		self::assertSame([], $container->requested);
		self::assertSame([
			IliasDemoDataPlugin::getName(), IDemoDataLifecycleService::class
		], $container->getServiceList());
		self::assertSame($plugin, $container->definitions[IliasDemoDataPlugin::getName()]['definition']);
		self::assertSame(IContainer::SHARED, $container->definitions[IliasDemoDataPlugin::getName()]['flags']);
		self::assertSame(IContainer::SHARED, $container->definitions[IDemoDataLifecycleService::class]['flags']);

		$factory = $container->definitions[IDemoDataLifecycleService::class]['definition'];
		self::assertInstanceOf(\Closure::class, $factory);
		self::assertSame([], $container->requested);
		$service = $factory($container);
		self::assertInstanceOf(IDemoDataLifecycleService::class, $service);
		self::assertInstanceOf(IliasDemoDataLifecycleService::class, $service);
		self::assertSame([Container::class], $container->requested);
	}
}
