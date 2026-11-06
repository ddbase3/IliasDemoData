<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

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
