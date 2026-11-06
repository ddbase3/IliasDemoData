<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasUserTracking {
	public static function _enabledLearningProgress(): bool {
		return DemoIliasState::$learningProgressEnabled;
	}
}
