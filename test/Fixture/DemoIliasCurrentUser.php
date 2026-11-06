<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasCurrentUser {

	public function getId(): int {
		return DemoIliasState::$currentUserId;
	}
}
