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

use Base3\CoreFoundation\Api\IRequest;

final class DemoIliasRequest implements IRequest {

	public function __construct(
		public array $query = [],
		public array $payload = [],
		public string $method = 'POST'
	) {}

	public function get(string $key, $default = null) { return $this->query[$key] ?? $default; }
	public function post(string $key, $default = null) { return $this->payload[$key] ?? $default; }
	public function request(string $key, $default = null) { return $this->payload[$key] ?? $this->query[$key] ?? $default; }
	public function allRequest(): array { return $this->payload + $this->query; }
	public function cookie(string $key, $default = null) { return $default; }
	public function session(string $key, $default = null) { return $default; }
	public function server(string $key, $default = null) { return $key === 'REQUEST_METHOD' ? $this->method : $default; }
	public function files(string $key, $default = null) { return $default; }
	public function allGet(): array { return $this->query; }
	public function allPost(): array { return $this->payload; }
	public function allCookie(): array { return []; }
	public function allSession(): array { return []; }
	public function allServer(): array { return ['REQUEST_METHOD' => $this->method]; }
	public function allFiles(): array { return []; }
	public function getJsonBody(): array { return $this->payload; }
	public function isCli(): bool { return false; }
	public function getContext(): string { return self::CONTEXT_TEST; }
}
