<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use Base3\Api\IContainer;
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
