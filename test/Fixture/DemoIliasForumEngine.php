<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasForumEngine {

	public function setForumId(int $id): void {}
	public function setForumRefId(int $refId): void {}
	public function setMDB2WhereCondition(string $sql, array $types, array $values): void {}
	public function getOneTopic(): DemoIliasTopicData { return new DemoIliasTopicData(); }
	public function generateThread(object $thread, string $body, bool $a, bool $b, bool $c): int {
		return (DemoIliasState::$switches['forum_thread_failure'] ?? false) ? 0 : DemoIliasState::$nextPostId++;
	}
}
