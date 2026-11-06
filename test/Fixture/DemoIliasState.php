<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use IliasDemoData\Seed\DemoContentCatalog;
use RuntimeException;

final class DemoIliasState {

	public static array $objects = [];
	public static array $references = [];
	public static array $users = [];
	public static array $roles = [];
	public static array $assignments = [];
	public static array $memberships = [];
	public static array $progress = [];
	public static array $switches = [];
	public static int $nextObjectId = 1000;
	public static int $nextReferenceId = 2000;
	public static int $nextPostId = 3000;
	public static int $currentUserId = 5;
	public static bool $administrator = true;
	public static bool $learningProgressEnabled = true;
	public static array $queries = [];
	public static array $deletedReferences = [];

	public static function reset(): void {
		self::$objects = [];
		self::$references = [1 => ['obj_id' => 1, 'parent' => 0, 'trashed' => false]];
		self::$users = [];
		self::$roles = [];
		self::$assignments = [];
		self::$memberships = [];
		self::$progress = [];
		self::$switches = [];
		self::$nextObjectId = 1000;
		self::$nextReferenceId = 2000;
		self::$nextPostId = 3000;
		self::$currentUserId = 5;
		self::$administrator = true;
		self::$learningProgressEnabled = true;
		self::$queries = [];
		self::$deletedReferences = [];
		self::$objects[1] = ['type' => 'cat', 'title' => 'Repository', 'description' => 'system'];
	}

	public static function titleType(string $title): string {
		foreach (DemoContentCatalog::repositoryDefinitions('BASE3 Demo Knowledge Lab') as $definition) {
			if ($definition['title'] === $title) {
				return $definition['type'];
			}
		}
		return 'aux';
	}

	public static function addUnmanaged(string $type, string $title, string $description, int $parent = 1): int {
		$id = self::$nextObjectId++;
		$ref = self::$nextReferenceId++;
		self::$objects[$id] = ['type' => $type, 'title' => $title, 'description' => $description];
		self::$references[$ref] = ['obj_id' => $id, 'parent' => $parent, 'trashed' => false];
		return $id;
	}

	public static function referencesOf(int $id): array {
		$result = [];
		foreach (self::$references as $ref => $record) {
			if ($record['obj_id'] === $id) $result[] = $ref;
		}
		return $result;
	}

	public static function fail(string $name): void {
		if (self::$switches[$name] ?? false) {
			throw new RuntimeException('Simulated ILIAS failure: ' . $name);
		}
	}

	public static function install(): void {
		foreach ([
			'DemoIliasContainer', 'DemoIliasCurrentUser', 'DemoIliasDatabase',
			'DemoIliasForumEngine', 'DemoIliasForumTopic', 'DemoIliasLearningProgress',
			'DemoIliasLmObject', 'DemoIliasMembers', 'DemoIliasObject',
			'DemoIliasObjectFactory', 'DemoIliasPage', 'DemoIliasParticipants',
			'DemoIliasRbac', 'DemoIliasRepository', 'DemoIliasRepositoryTree',
			'DemoIliasRole', 'DemoIliasStreams', 'DemoIliasTopicData',
			'DemoIliasUser', 'DemoIliasUserTracking'
		] as $fixtureName) {
			require_once __DIR__ . '/' . $fixtureName . '.php';
		}

		$aliases = [
			'ILIAS\\DI\\Container' => DemoIliasContainer::class,
			'ILIAS\\Filesystem\\Stream\\Streams' => DemoIliasStreams::class,
			'ilObject' => DemoIliasObject::class,
			'ilObjCategory' => DemoIliasObject::class,
			'ilObjCourse' => DemoIliasObject::class,
			'ilObjWiki' => DemoIliasObject::class,
			'ilObjBlog' => DemoIliasObject::class,
			'ilObjForum' => DemoIliasObject::class,
			'ilObjLearningModule' => DemoIliasObject::class,
			'ilObjFile' => DemoIliasObject::class,
			'ilStructureObject' => DemoIliasObject::class,
			'ilLMPageObject' => DemoIliasObject::class,
			'ilLMObject' => DemoIliasLmObject::class,
			'ilTree' => DemoIliasObject::class,
			'ilObjUser' => DemoIliasUser::class,
			'ilObjRole' => DemoIliasRole::class,
			'ilObjUserTracking' => DemoIliasUserTracking::class,
			'ilLPStatus' => DemoIliasLearningProgress::class,
			'ilParticipants' => DemoIliasParticipants::class,
			'ilBlogPosting' => DemoIliasPage::class,
			'ilPageObject' => DemoIliasPage::class,
			'ilForumTopic' => DemoIliasForumTopic::class,
			'ilObjectFactory' => DemoIliasObjectFactory::class,
		];
		foreach ($aliases as $name => $fixture) {
			if (class_exists($name, false)) {
				throw new RuntimeException('A native ILIAS class was already loaded: ' . $name);
			}
			class_alias($fixture, $name, true);
		}
		if (!defined('SYSTEM_ROLE_ID')) define('SYSTEM_ROLE_ID', 2);
		if (!defined('ROLE_FOLDER_ID')) define('ROLE_FOLDER_ID', 3);
		if (!defined('ROOT_FOLDER_ID')) define('ROOT_FOLDER_ID', 1);
		self::reset();
	}
}
