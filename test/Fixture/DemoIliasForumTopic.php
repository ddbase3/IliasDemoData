<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasForumTopic {

	public function __construct(int $id, bool $a, bool $b) {}
	public function setForumId(int $id): void {}
	public function setThrAuthorId(int $id): void {}
	public function setDisplayUserId(int $id): void {}
	public function setSubject(string $title): void {}
	public function setUserAlias(string $alias): void {}
}
