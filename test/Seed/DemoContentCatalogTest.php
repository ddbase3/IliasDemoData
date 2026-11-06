<?php declare(strict_types=1);

namespace IliasDemoData\Test\Seed;

use IliasDemoData\Seed\DemoContentCatalog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class DemoContentCatalogTest extends TestCase {

	public function testFixtureSizesAndStatsAreStable(): void {
		self::assertCount(14, DemoContentCatalog::users());
		self::assertCount(5, DemoContentCatalog::categories());
		self::assertCount(4, DemoContentCatalog::courses());
		self::assertCount(4, DemoContentCatalog::wikis());
		self::assertCount(3, DemoContentCatalog::blogs());
		self::assertCount(4, DemoContentCatalog::forums());
		self::assertCount(4, DemoContentCatalog::learningModules());
		self::assertCount(6, DemoContentCatalog::files());
		self::assertSame([
			'users' => 14, 'courses' => 4, 'wiki_pages' => 21,
			'blog_posts' => 12, 'forum_threads' => 12,
			'learning_module_pages' => 18, 'files' => 6,
			'approx_words' => 35375,
		], DemoContentCatalog::contentStats());
		self::assertSame(31, DemoContentCatalog::expectedRepositoryObjectCount());
	}

	public function testAllUserLoginsAreUniqueAndUseInvalidDemoAddresses(): void {
		$seen = [];
		foreach (DemoContentCatalog::users() as $user) {
			self::assertMatchesRegularExpression('/^demo\.[a-z]+\.[a-z]+$/', $user['login']);
			self::assertStringEndsWith('@demo.invalid', $user['email']);
			self::assertNotEmpty($user['firstname']);
			self::assertNotEmpty($user['lastname']);
			self::assertArrayNotHasKey($user['login'], $seen);
			$seen[$user['login']] = true;
		}
	}

	public function testAllMembershipsReferenceRealUsersAndValidStates(): void {
		$users = array_column(DemoContentCatalog::users(), null, 'login');
		$knownCourseKeys = [];
		$hasTutor = false;
		$hasMember = false;
		foreach (DemoContentCatalog::courses() as $course) {
			self::assertArrayNotHasKey($course['key'], $knownCourseKeys);
			$knownCourseKeys[$course['key']] = true;
			self::assertNotEmpty($course['memberships']);
			foreach ($course['memberships'] as $login => $membership) {
				self::assertArrayHasKey($login, $users);
				self::assertContains($membership['role'], ['member', 'tutor']);
				self::assertContains($membership['status'], ['not_attempted', 'in_progress', 'completed', 'failed']);
				self::assertGreaterThanOrEqual(0, $membership['percentage']);
				self::assertLessThanOrEqual(100, $membership['percentage']);
				$hasTutor = $hasTutor || $membership['role'] === 'tutor';
				$hasMember = $hasMember || $membership['role'] === 'member';
			}
		}
		self::assertTrue($hasTutor);
		self::assertTrue($hasMember);
	}

	public function testRepositoryDefinitionsAreUniqueAndMatchAllOwnedObjectFamilies(): void {
		$root = 'Test root';
		$definitions = DemoContentCatalog::repositoryDefinitions($root);
		self::assertSame(['type' => 'cat', 'title' => $root], $definitions[0]);
		self::assertCount(DemoContentCatalog::expectedRepositoryObjectCount(), $definitions);
		self::assertSame([
			'cat' => 6, 'crs' => 4, 'wiki' => 4, 'blog' => 3,
			'frm' => 4, 'lm' => 4, 'file' => 6,
		], array_count_values(array_column($definitions, 'type')));
		$ids = array_map(static fn(array $r): string => $r['type'] . ':' . $r['title'], $definitions);
		self::assertCount(count($ids), array_unique($ids));
	}

	public function testWikiBlogForumAndModuleEntriesContainSearchableText(): void {
		foreach ([
			[DemoContentCatalog::wikis(), 'pages'],
			[DemoContentCatalog::blogs(), 'posts'],
			[DemoContentCatalog::forums(), 'threads'],
			[DemoContentCatalog::learningModules(), 'pages'],
		] as [$objects, $childKey]) {
			foreach ($objects as $object) {
				self::assertNotEmpty($object['title']);
				self::assertNotEmpty($object[$childKey]);
				foreach ($object[$childKey] as $entry) {
					self::assertNotEmpty($entry['title']);
					self::assertGreaterThan(100, str_word_count($entry['text']));
					self::assertStringContainsString('fictional Cedar Vale Instruments', $entry['text']);
				}
			}
		}
		foreach (DemoContentCatalog::files() as $file) {
			self::assertStringEndsWith('.txt', $file['name']);
			self::assertGreaterThan(100, str_word_count($file['text']));
		}
	}

	public function testCrossReferencesPointToConfiguredCourseAndCategoryKeys(): void {
		$categories = array_column(DemoContentCatalog::categories(), null, 'key');
		$courses = array_column(DemoContentCatalog::courses(), null, 'key');
		foreach ([DemoContentCatalog::wikis(), DemoContentCatalog::blogs(), DemoContentCatalog::files()] as $objects) {
			foreach ($objects as $entry) self::assertArrayHasKey($entry['parent'], $categories);
		}
		foreach ([DemoContentCatalog::forums(), DemoContentCatalog::learningModules()] as $objects) {
			foreach ($objects as $entry) self::assertArrayHasKey($entry['course'], $courses);
		}
	}

	public static function articleStructures(): array {
		return [
			'minimal' => [[], [], false, false],
			'procedures only' => [['step one', 'step two'], [], true, false],
			'related only' => [[], ['reference-a'], false, true],
			'both optional parts' => [['step'], ['reference'], true, true],
		];
	}

	#[DataProvider('articleStructures')]
	public function testArticleAssemblyHandlesAllOptionalSections(
		array $procedures, array $related, bool $hasProcedure, bool $hasRelated
	): void {
		$method = new ReflectionMethod(DemoContentCatalog::class, 'article');
		$text = $method->invoke(null, 'Example title', 'Example summary', ['one', 'two'], $procedures, $related);
		self::assertStringStartsWith('Example title. Example summary', $text);
		self::assertStringContainsString('Key fact 1: one', $text);
		self::assertStringContainsString('Key fact 2: two', $text);
		self::assertSame($hasProcedure, str_contains($text, 'Procedure guidance:'));
		self::assertSame($hasRelated, str_contains($text, 'Related demo sources:'));
		self::assertStringContainsString('Quality note:', $text);
	}
}
