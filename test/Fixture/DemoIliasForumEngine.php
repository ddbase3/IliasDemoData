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

final class DemoIliasForumEngine {

	public function setForumId(int $id): void {}
	public function setForumRefId(int $refId): void {}
	public function setMDB2WhereCondition(string $sql, array $types, array $values): void {}
	public function getOneTopic(): DemoIliasTopicData { return new DemoIliasTopicData(); }
	public function generateThread(object $thread, string $body, bool $a, bool $b, bool $c): int {
		return (DemoIliasState::$switches['forum_thread_failure'] ?? false) ? 0 : DemoIliasState::$nextPostId++;
	}
}
