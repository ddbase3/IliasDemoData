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

use Base3\CoreFoundation\Api\IContainer;
use RuntimeException;

final class DemoIliasContainerSpy implements IContainer {

	public array $definitions = [];
	public array $requested = [];

	public function __construct(private readonly array $services = []) {}

	public function getServiceList(): array {
		return array_keys($this->definitions);
	}

	public function set(string $name, $classDefinition, $flags = 0): IContainer {
		$this->definitions[$name] = ['definition' => $classDefinition, 'flags' => $flags];
		return $this;
	}

	public function remove(string $name) {
		unset($this->definitions[$name]);
	}

	public function has(string $name): bool {
		return isset($this->definitions[$name]);
	}

	public function get(string $name) {
		$this->requested[] = $name;
		if (!array_key_exists($name, $this->services)) {
			throw new RuntimeException('Unexpected service resolution: ' . $name);
		}
		return $this->services[$name];
	}
}
