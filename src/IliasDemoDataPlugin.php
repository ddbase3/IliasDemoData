<?php declare(strict_types=1);

namespace IliasDemoData;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;
use IliasDemoData\Api\IDemoDataLifecycleService;
use IliasDemoData\Service\IliasDemoDataLifecycleService;
use ILIAS\DI\Container;

final class IliasDemoDataPlugin implements IPlugin {

	public function __construct(
		private readonly IContainer $container
	) {}

	public static function getName(): string {
		return 'iliasdemodata';
	}

	public function init(): void {
		$this->container
			->set(self::getName(), $this, IContainer::SHARED)
			->set(
				IDemoDataLifecycleService::class,
				fn($c) => new IliasDemoDataLifecycleService($c->get(Container::class)),
				IContainer::SHARED
			);
	}
}
