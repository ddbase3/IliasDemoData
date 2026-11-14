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

final class DemoIliasRbac {

	public function review(): self {
		return $this;
	}

	public function admin(): self {
		return $this;
	}

	public function isAssigned(int $userId, int $roleId): bool {
		if ($roleId === SYSTEM_ROLE_ID) return DemoIliasState::$administrator && $userId === DemoIliasState::$currentUserId;
		return DemoIliasState::$assignments[$roleId][$userId] ?? false;
	}

	public function assignUser(int $roleId, int $userId): void {
		if (!(DemoIliasState::$switches['role_assignment'] ?? false)) {
			DemoIliasState::$assignments[$roleId][$userId] = true;
		}
	}

	public function assignRoleToFolder(int $roleId, int $folderId, string $status): void {
		DemoIliasState::fail('assign_role_folder');
	}

	public function getGlobalRoles(): array {
		return array_keys(DemoIliasState::$roles);
	}

	public function assignedUsers(int $roleId): array {
		return array_keys(array_filter(DemoIliasState::$assignments[$roleId] ?? []));
	}
}
