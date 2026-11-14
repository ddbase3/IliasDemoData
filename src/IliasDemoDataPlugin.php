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


namespace Base3\IliasDemoData;

use Base3\CoreFoundation\Api\IContainer;
use Base3\CoreFoundation\Api\IPlugin;
use Base3\IliasDemoData\Api\IDemoDataLifecycleService;
use Base3\IliasDemoData\Service\IliasDemoDataLifecycleService;
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
