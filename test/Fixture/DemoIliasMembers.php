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

final class DemoIliasMembers {

	public function __construct(private readonly int $courseId) {}

	public function add(int $userId, int $role): bool {
		if (DemoIliasState::$switches['membership_add_failure'] ?? false) return false;
		DemoIliasState::$memberships[$this->courseId][$userId] = ['role' => $role, 'passed' => false];
		return true;
	}

	public function isAssigned(int $userId): bool {
		if (DemoIliasState::$switches['membership_verify_failure'] ?? false) return false;
		return isset(DemoIliasState::$memberships[$this->courseId][$userId]);
	}

	public function updatePassed(int $userId, bool $passed, bool $one, bool $two): void {
		DemoIliasState::$memberships[$this->courseId][$userId]['passed'] = $passed;
	}
}
