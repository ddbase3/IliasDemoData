<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use Base3\Authorization\Api\IAuthorizationService;
use Base3\Authorization\Dto\AuthorizationDecision;
use Base3\Authorization\Dto\AuthorizationRequest;

final class DemoIliasAuthorization implements IAuthorizationService {

	public array $requests = [];

	public function __construct(public bool $native = true, public bool $feature = true) {}
	public function getDefaultAuthority(): string { return 'ilias'; }
	public function decide(AuthorizationRequest $request): AuthorizationDecision {
		$this->requests[] = $request;
		$allowed = $request->permission->scope === 'system' ? $this->native : $this->feature;
		return $allowed ? AuthorizationDecision::allow() : AuthorizationDecision::deny();
	}
	public function decideMany(array $requests): array {
		$result = [];
		foreach ($requests as $key => $request) $result[$key] = $this->decide($request);
		return $result;
	}
}
