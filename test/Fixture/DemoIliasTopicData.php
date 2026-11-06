<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasTopicData {
	public function getTopPk(): int {
		return (DemoIliasState::$switches['forum_topic_failure'] ?? false) ? 0 : 1;
	}
}
