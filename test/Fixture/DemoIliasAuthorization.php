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

use Base3\CoreFoundation\Authorization\Api\IAuthorizationService;
use Base3\CoreFoundation\Authorization\Dto\AuthorizationDecision;
use Base3\CoreFoundation\Authorization\Dto\AuthorizationRequest;

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
