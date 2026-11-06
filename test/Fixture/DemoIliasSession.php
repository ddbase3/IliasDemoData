<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use Base3\Session\Api\ISession;

final class DemoIliasSession implements ISession {

	public array $values = [];
	public int $writes = 0;
	public function started(): bool { return true; }
	public function getId(): string { return 'test-session'; }
	public function start(): bool { return true; }
	public function destroy(): bool { $this->values = []; return true; }
	public function get(string $key, mixed $default = null): mixed { return $this->values[$key] ?? $default; }
	public function set(string $key, mixed $value): void { $this->values[$key] = $value; $this->writes++; }
	public function has(string $key): bool { return array_key_exists($key, $this->values); }
	public function remove(string $key): void { unset($this->values[$key]); }
}
