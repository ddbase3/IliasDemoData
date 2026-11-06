<?php declare(strict_types=1);

namespace IliasDemoData\Test\Service;

use IliasDemoData\Api\IDemoDataLifecycleService;
use IliasDemoData\Seed\DemoContentCatalog;
use IliasDemoData\Service\IliasDemoDataLifecycleService;
use IliasDemoData\Test\Fixture\DemoIliasState;
use ILIAS\DI\Container;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class IliasDemoDataLifecycleServiceTest extends TestCase {

	private IliasDemoDataLifecycleService $service;

	protected function setUp(): void {
		require_once dirname(__DIR__) . '/Fixture/DemoIliasState.php';
		DemoIliasState::install();
		$this->service = new IliasDemoDataLifecycleService(new Container());
	}

	public function testEmptyRepositoryHasPreciseUninstalledStatus(): void {
		self::assertInstanceOf(IDemoDataLifecycleService::class, $this->service);
		$status = $this->service->getStatus(1);
		self::assertTrue($status['parent_valid']);
		self::assertTrue($status['is_system_admin']);
		self::assertNull($status['root']);
		self::assertSame([], $status['managed_roots']);
		self::assertSame([], $status['managed_repository_object_ids']);
		self::assertSame([], $status['users']);
		self::assertNull($status['role']);
		self::assertFalse($status['repository_complete']);
		self::assertFalse($status['role_assignments_complete']);
		self::assertFalse($status['membership_status']['complete']);
		self::assertFalse($status['complete']);
		self::assertSame(DemoContentCatalog::expectedRepositoryObjectCount(), $status['expected_repository_object_count']);
		self::assertSame(count(DemoContentCatalog::users()), $status['expected_user_count']);
		self::assertSame(DemoContentCatalog::contentStats(), $status['content_stats']);
	}

	public function testMissingAndNonPositiveParentsAreInvalid(): void {
		foreach ([0, -5, 9999] as $ref) {
			self::assertFalse($this->service->getStatus($ref)['parent_valid']);
		}
	}

	public function testCompleteCreateStatusAndRemoveLifecycle(): void {
		$result = $this->service->create(1, '  shared-password  ');
		self::assertSame('', $result['generated_password']);
		self::assertCount(12, $result['steps']);
		self::assertSame('Demo root category', $result['steps'][0]['name']);
		self::assertSame('RAG corpus', $result['steps'][11]['name']);
		self::assertSame(count(DemoContentCatalog::users()), $result['steps'][3]['id']);
		$status = $this->service->getStatus(1);
		self::assertTrue($status['complete']);
		self::assertTrue($status['repository_complete']);
		self::assertTrue($status['role_assignments_complete']);
		self::assertTrue($status['membership_status']['complete']);
		self::assertCount(1, $status['managed_roots']);
		self::assertCount($status['expected_repository_object_count'], $status['managed_repository_object_ids']);
		self::assertCount(14, $status['users']);
		self::assertSame($status['membership_status']['expected'], $status['membership_status']['assigned']);
		self::assertSame($status['membership_status']['expected'], $status['membership_status']['progress_verified']);
		self::assertNotEmpty($status['root']['ref_id']);

		$removed = $this->service->remove(1);
		self::assertSame('', $removed['generated_password']);
		self::assertCount(3, $removed['steps']);
		self::assertSame('Repository demo subtree', $removed['steps'][0]['name']);
		self::assertSame('Global demo role', $removed['steps'][2]['name']);
		self::assertFalse($this->service->getStatus(1)['complete']);
		self::assertSame([], $this->service->getStatus(1)['managed_repository_object_ids']);
		self::assertSame([], $this->service->getStatus(1)['users']);
		self::assertCount(2, DemoIliasState::$queries);
		self::assertStringContainsString('obj_members', DemoIliasState::$queries[0]);
		self::assertStringContainsString('ut_lp_marks', DemoIliasState::$queries[1]);
	}

	public function testPasswordIsGeneratedOnlyWhenBlank(): void {
		$result = $this->service->create(1, " \t\n ");
		self::assertMatchesRegularExpression('/^Base3Demo-[0-9a-f]{16}!$/', $result['generated_password']);
		self::assertSame($result['generated_password'], reset(DemoIliasState::$users)['password']);
	}

	public function testLearningProgressDisabledUsesPassedFlags(): void {
		DemoIliasState::$learningProgressEnabled = false;
		$this->service->create(1, 'pw');
		$status = $this->service->getStatus(1);
		self::assertFalse($status['learning_progress_enabled']);
		self::assertTrue($status['complete']);
		self::assertSame([], DemoIliasState::$progress);
		self::assertContains(true, array_column(array_merge(...array_values(DemoIliasState::$memberships)), 'passed'));
	}

	public function testAnUnmanagedObjectSurvivesTheManagedLifecycle(): void {
		$id = DemoIliasState::addUnmanaged('cat', 'Unrelated category', 'do not remove');
		$this->service->create(1, 'pw');
		$this->service->remove(1);
		self::assertArrayHasKey($id, DemoIliasState::$objects);
		self::assertSame('do not remove', DemoIliasState::$objects[$id]['description']);
	}

	public function testCreateAndRemoveAreDeniedToNonAdministrators(): void {
		DemoIliasState::$administrator = false;
		foreach (['create', 'remove'] as $action) {
			try {
				$this->service->$action(1);
				self::fail('A non-administrator was allowed to ' . $action);
			} catch (RuntimeException $e) {
				self::assertStringContainsString('system administrator', $e->getMessage());
			}
		}
		self::assertSame([], DemoIliasState::$users);
	}

	public function testInvalidRepositoryParentIsRejectedBeforeWriting(): void {
		foreach (['create', 'remove'] as $action) {
			try {
				$this->service->$action(0);
				self::fail('Invalid parent accepted');
			} catch (RuntimeException $e) {
				self::assertStringContainsString('existing ILIAS repository reference', $e->getMessage());
			}
		}
	}

	public function testCreatingTwiceNeverOverwritesManagedData(): void {
		$this->service->create(1, 'pw');
		$before = DemoIliasState::$objects;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('already exists');
		try { $this->service->create(1, 'new-password'); } finally {
			self::assertSame($before, DemoIliasState::$objects);
		}
	}

	public function testUnmarkedRootTitleIsNeverReusedOrDeleted(): void {
		$id = DemoIliasState::addUnmanaged('cat', 'BASE3 Demo Knowledge Lab', 'owner is someone else');
		foreach (['create', 'remove'] as $action) {
			try {
				$this->service->$action(1);
				self::fail('Unmanaged root touched');
			} catch (RuntimeException $e) {
				self::assertStringContainsString('ownership marker', $e->getMessage());
			}
		}
		self::assertArrayHasKey($id, DemoIliasState::$objects);
	}

	public function testUnmanagedRoleTitlePreventsCreateAndRemove(): void {
		$id = DemoIliasState::addUnmanaged('role', 'BASE3 Demo Users', 'foreign role');
		DemoIliasState::$roles[$id] = true;
		foreach (['create', 'remove'] as $action) {
			try {
				$this->service->$action(1);
				self::fail('Unmanaged role touched');
			} catch (RuntimeException $e) {
				self::assertStringContainsString('role title', $e->getMessage());
			}
		}
		self::assertArrayHasKey($id, DemoIliasState::$roles);
	}

	public function testUnmarkedDemoLoginBlocksAllMutations(): void {
		$existing = DemoContentCatalog::users()[0];
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$users[$id] = $existing + ['external' => 'a different owner'];
		foreach (['create', 'remove'] as $action) {
			try {
				$this->service->$action(1);
				self::fail('Foreign login modified');
			} catch (RuntimeException $e) {
				self::assertStringContainsString('ownership marker', $e->getMessage());
			}
		}
		self::assertSame('a different owner', DemoIliasState::$users[$id]['external']);
	}

	public function testOnlyKnownOwnedUsersAppearInStatus(): void {
		$user = DemoContentCatalog::users()[0];
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$users[$id] = $user + ['external' => 'unowned'];
		self::assertSame([], $this->service->getStatus(1)['users']);
		DemoIliasState::$users[$id]['external'] = 'base3-ilias-demo-data:seed:v1:' . $user['login'];
		self::assertCount(1, $this->service->getStatus(1)['users']);
	}

	public function testRoleAssignmentsAreVerifiedIndividually(): void {
		$this->service->create(1, 'pw');
		$roleId = (int)$this->service->getStatus(1)['role']['id'];
		$userId = (int)$this->service->getStatus(1)['users'][0]['id'];
		unset(DemoIliasState::$assignments[$roleId][$userId]);
		$status = $this->service->getStatus(1);
		self::assertFalse($status['role_assignments_complete']);
		self::assertFalse($status['complete']);
	}

	public function testMissingOneManagedRepositoryObjectMakesStatusIncomplete(): void {
		$this->service->create(1, 'pw');
		$rootId = $this->service->getStatus(1)['root']['obj_id'];
		foreach (DemoIliasState::$objects as $id => $row) {
			if ($id !== $rootId && $row['type'] === 'wiki') {
				unset(DemoIliasState::$objects[$id]);
				break;
			}
		}
		self::assertFalse($this->service->getStatus(1)['repository_complete']);
	}

	public function testMismatchedProgressIsDetected(): void {
		$this->service->create(1, 'pw');
		$courseId = array_key_first(DemoIliasState::$progress);
		$userId = array_key_first(DemoIliasState::$progress[$courseId]);
		DemoIliasState::$progress[$courseId][$userId]['percentage'] = -1;
		$status = $this->service->getStatus(1);
		self::assertFalse($status['membership_status']['complete']);
		self::assertFalse($status['complete']);
	}

	public function testCreatingWithMissingRoleAssignmentTriggersManagedRollback(): void {
		DemoIliasState::$switches['role_assignment'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('managed partial dataset was removed');
		try { $this->service->create(1, 'pw'); } finally {
			self::assertSame([], $this->service->getStatus(1)['managed_roots']);
		}
	}

	public function testFailedCreationWithFailedCleanupPreservesOriginalCause(): void {
		DemoIliasState::$switches['role_assignment'] = true;
		DemoIliasState::$switches['retain_references'] = true;
		try {
			$this->service->create(1, 'pw');
			self::fail('Creation failure not thrown');
		} catch (RuntimeException $e) {
			self::assertStringContainsString('Cleanup of the managed partial dataset also failed', $e->getMessage());
			self::assertStringContainsString('still exists after deletion', $e->getMessage());
			self::assertNotNull($e->getPrevious());
		}
	}

	public function testFailedPageXmlIsRolledBack(): void {
		DemoIliasState::$switches['reject_xml'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('ILIAS rejected managed demo page XML content');
		$this->service->create(1, 'pw');
	}

	public function testFileWithoutResourceIdFailsBeforeReportingSuccess(): void {
		DemoIliasState::$switches['missing_file_resource'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('has no ResourceStorage resource id');
		$this->service->create(1, 'pw');
	}

	public function testForumTopicInitializationFailureTriggersRollback(): void {
		DemoIliasState::$switches['forum_topic_failure'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('could not be initialized');
		$this->service->create(1, 'pw');
	}

	public function testForumThreadCreationFailureTriggersRollback(): void {
		DemoIliasState::$switches['forum_thread_failure'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('could not be created');
		$this->service->create(1, 'pw');
	}

	public static function membershipFailures(): array {
		return [
			'course status determination' => ['wrong_status_determination', 'does not use learning-progress'],
			'course assignment write' => ['membership_add_failure', 'could not be added'],
			'course assignment read' => ['membership_verify_failure', 'could not be verified'],
			'learning progress verification' => ['invalid_progress', 'could not be verified after writing'],
		];
	}

	#[DataProvider('membershipFailures')]
	public function testMembershipFailuresStopCreationAndTriggerCleanup(string $failure, string $message): void {
		DemoIliasState::$switches[$failure] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage($message);
		$this->service->create(1, 'pw');
	}

	public function testRemoveWithoutOwnedDataIsIdempotent(): void {
		$result = $this->service->remove(1);
		self::assertSame([], $result['steps']);
		self::assertStringContainsString('No managed BASE3 ILIAS demo data', $result['message']);
	}

	public function testRemovalRejectsRoleStillAssignedToUsers(): void {
		$this->service->create(1, 'pw');
		$roleId = $this->service->getStatus(1)['role']['id'];
		DemoIliasState::$assignments[$roleId][98765] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('still has assigned users');
		$this->service->remove(1);
	}

	public function testRemovalRejectsFailedRoleDeletion(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['role_delete_failure'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('could not be deleted through ilObjRole::delete');
		$this->service->remove(1);
	}

	public function testRemovalFailsIfUserCannotBeDeleted(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['retain_users'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('still exists after deletion');
		$this->service->remove(1);
	}

	public function testRemovalFailsIfRepositoryRootCannotBeDeleted(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['retain_references'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('still exists after deletion');
		$this->service->remove(1);
	}

	public function testRemovalFailsIfRuntimeRowsRemain(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['runtime_rows'] = 'ut_lp_marks';
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Managed demo runtime rows remain in ut_lp_marks');
		$this->service->remove(1);
	}

	public function testNoDatabaseCheckIsNeededForEmptyManagedState(): void {
		$this->service->remove(1);
		self::assertSame([], DemoIliasState::$queries);
	}

	public function testUnmanagedRootWithMatchingTitleButWrongTypeIsIgnored(): void {
		DemoIliasState::addUnmanaged('crs', 'BASE3 Demo Knowledge Lab', 'other');
		self::assertSame([], $this->service->getStatus(1)['managed_roots']);
		self::assertNull($this->service->getStatus(1)['root']);
	}

	public function testManagedRootReferencesAreOrderedAndCanBeTrashed(): void {
		$this->service->create(1, 'pw');
		$root = $this->service->getStatus(1)['root'];
		$additionalRef = DemoIliasState::$nextReferenceId++;
		DemoIliasState::$references[$additionalRef] = [
			'obj_id' => $root['obj_id'], 'parent' => 1, 'trashed' => true
		];
		$status = $this->service->getStatus(1);
		self::assertCount(2, $status['managed_roots']);
		self::assertTrue($status['managed_roots'][1]['trashed']);
		self::assertFalse($status['repository_complete']);
	}

	public function testOwnedMarkerOnRoleIsRequiredForStatus(): void {
		$this->service->create(1, 'pw');
		$id = $this->service->getStatus(1)['role']['id'];
		DemoIliasState::$objects[$id]['description'] = 'not owned';
		self::assertNull($this->service->getStatus(1)['role']);
	}

	public function testXmlTextIsEscapedAndRejectionIsReported(): void {
		$page = new \ilPageObject();
		$method = new ReflectionMethod($this->service, 'writePageText');
		$method->invoke($this->service, $page, 'a < b & "quoted"');
		self::assertStringContainsString('a &lt; b &amp; &quot;quoted&quot;', DemoIliasState::$switches['last_xml']);
		DemoIliasState::$switches['reject_xml'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('rejected managed demo page XML');
		$method->invoke($this->service, $page, 'invalid');
	}

	public function testRepositoryStepDefaultsToObjectTitleAndAcceptsDetails(): void {
		$object = new \ilObjCategory();
		$object->setTitle('sample');
		$object->create();
		$object->createReference();
		$method = new ReflectionMethod($this->service, 'repositoryStep');
		$default = $method->invoke($this->service, 'step', $object);
		$explicit = $method->invoke($this->service, 'step', $object, 'override');
		self::assertSame('sample', $default['detail']);
		self::assertSame('override', $explicit['detail']);
		self::assertSame($object->getId(), $default['id']);
		self::assertSame($object->getRefId(), $explicit['ref_id']);
	}

	public function testMissingWikiPagesCannotBeSeeded(): void {
		$method = new ReflectionMethod($this->service, 'createWiki');
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('has no pages');
		$method->invoke($this->service, ['title' => 'empty wiki', 'pages' => []], 1);
	}

	public function testMissingCourseOrUserBlocksMembershipSeeding(): void {
		$method = new ReflectionMethod($this->service, 'seedCourseMemberships');
		try {
			$method->invoke($this->service, [], []);
			self::fail('Missing course accepted');
		} catch (RuntimeException $e) {
			self::assertStringContainsString('is missing while seeding memberships', $e->getMessage());
		}
		$course = new \ilObjCourse();
		$course->setTitle(DemoContentCatalog::courses()[0]['title']);
		$course->create();
		$course->setStatusDetermination(\ilObjCourse::STATUS_DETERMINATION_LP);
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('is missing while seeding course memberships');
		$method->invoke($this->service, [DemoContentCatalog::courses()[0]['key'] => $course], []);
	}
	public function testOrphanedManagedObjectIsDeletedWithoutRoot(): void {
		$id = DemoIliasState::addUnmanaged('wiki', DemoContentCatalog::wikis()[0]['title'],
			'[base3-ilias-demo-data:seed:v1] Managed demo seed object.');
		self::assertCount(1, $this->service->getStatus(1)['managed_repository_object_ids']);
		$result = $this->service->remove(1);
		self::assertArrayNotHasKey($id, DemoIliasState::$objects);
		self::assertSame([], $result['steps']);
		self::assertSame([], $this->service->getStatus(1)['managed_repository_object_ids']);
	}

	public function testOrphanedManagedObjectWithNoReferencesIsDeletedThroughObjectLifecycle(): void {
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$objects[$id] = [
			'type' => 'wiki', 'title' => DemoContentCatalog::wikis()[0]['title'],
			'description' => '[base3-ilias-demo-data:seed:v1] Managed demo seed object.'
		];
		$this->service->remove(1);
		self::assertArrayNotHasKey($id, DemoIliasState::$objects);
	}

	public function testMissingOrphanInstanceCausesRemovalToFailClosed(): void {
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$objects[$id] = [
			'type' => 'wiki', 'title' => DemoContentCatalog::wikis()[0]['title'],
			'description' => '[base3-ilias-demo-data:seed:v1] Managed demo seed object.'
		];
		DemoIliasState::$switches['missing_orphan'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('could not be loaded for orphan cleanup');
		$this->service->remove(1);
	}

	public function testRetainedOrphanAfterDeletionFailsVerification(): void {
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$objects[$id] = [
			'type' => 'wiki', 'title' => DemoContentCatalog::wikis()[0]['title'],
			'description' => '[base3-ilias-demo-data:seed:v1] Managed demo seed object.'
		];
		DemoIliasState::$switches['retain_orphan'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('still exists after lifecycle deletion');
		$this->service->remove(1);
	}

	public function testUnmanagedOrphanWithSimilarTitleIsNeverRemoved(): void {
		$id = DemoIliasState::addUnmanaged('wiki', DemoContentCatalog::wikis()[0]['title'], 'foreign ownership');
		$this->service->remove(1);
		self::assertArrayHasKey($id, DemoIliasState::$objects);
	}

	public function testMissingManagedCourseOrUserMakesMembershipStatusIncomplete(): void {
		$this->service->create(1, 'pw');
		foreach (DemoIliasState::$objects as $id => $row) {
			if ($row['type'] === 'crs') {
				unset(DemoIliasState::$objects[$id]);
				break;
			}
		}
		$status = $this->service->getStatus(1);
		self::assertFalse($status['membership_status']['complete']);
		self::assertLessThanOrEqual($status['membership_status']['expected'] - 1, $status['membership_status']['assigned']);
	}

	public function testAnUnassignedCourseMemberIsNotReportedAsVerified(): void {
		$this->service->create(1, 'pw');
		$courseId = array_key_first(DemoIliasState::$memberships);
		$userId = array_key_first(DemoIliasState::$memberships[$courseId]);
		unset(DemoIliasState::$memberships[$courseId][$userId]);
		$status = $this->service->getStatus(1);
		self::assertFalse($status['membership_status']['complete']);
	}

	public function testOwnedUserOnlyCleanupVerifiesUserRuntimeRows(): void {
		$user = DemoContentCatalog::users()[0];
		$id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$users[$id] = $user + ['external' => 'base3-ilias-demo-data:seed:v1:' . $user['login']];
		DemoIliasState::$objects[$id] = ['type' => 'usr', 'title' => $user['login'], 'description' => ''];
		$this->service->remove(1);
		self::assertCount(2, DemoIliasState::$queries);
		self::assertStringContainsString('usr_id IN', DemoIliasState::$queries[0]);
		self::assertStringNotContainsString('obj_id IN', DemoIliasState::$queries[0]);
	}

	public function testPartialCourseOnlyCleanupVerifiesCourseRuntimeRows(): void {
		$id = DemoIliasState::addUnmanaged('crs', DemoContentCatalog::courses()[0]['title'],
			'[base3-ilias-demo-data:seed:v1] Managed demo seed object.');
		$this->service->remove(1);
		self::assertArrayNotHasKey($id, DemoIliasState::$objects);
		self::assertCount(2, DemoIliasState::$queries);
		self::assertStringContainsString('obj_id IN', DemoIliasState::$queries[0]);
		self::assertStringNotContainsString('usr_id IN', DemoIliasState::$queries[0]);
	}

	public function testRoleFolderAssignmentFailureRollsBackTheDataset(): void {
		DemoIliasState::$switches['assign_role_folder'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Simulated ILIAS failure: assign_role_folder');
		$this->service->create(1, 'pw');
	}

	public function testUserUpdateFailureRollsBackTheDataset(): void {
		DemoIliasState::$switches['user_update'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Simulated ILIAS failure: user_update');
		$this->service->create(1, 'pw');
	}

	public function testCreateFailsIfFinalDatasetVerificationIsIncomplete(): void {
		DemoIliasState::$switches['omit_wiki_from_status'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('resulting managed dataset is incomplete');
		$this->service->create(1, 'pw');
	}

	public function testCreationFailureBeforeRootExistsIsRolledBack(): void {
		DemoIliasState::$switches['object_create'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('managed partial dataset was removed');
		$this->service->create(1, 'pw');
	}

	public function testRemovalRejectsRoleThatStillExistsAfterDeleteReturnsTrue(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['retain_role_after_delete'] = true;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('demo role still exists after deletion');
		$this->service->remove(1);
	}

	public function testOrphanInTrashDoesNotNeedSecondTrashTransition(): void {
		$id = DemoIliasState::addUnmanaged('wiki', DemoContentCatalog::wikis()[0]['title'],
			'[base3-ilias-demo-data:seed:v1] Managed demo seed object.');
		$ref = DemoIliasState::referencesOf($id)[0];
		DemoIliasState::$references[$ref]['trashed'] = true;
		$this->service->remove(1);
		self::assertNotContains($ref, DemoIliasState::$deletedReferences);
		self::assertArrayNotHasKey($id, DemoIliasState::$objects);
	}

	public function testRuntimeMembershipRowsPreventRemoval(): void {
		$this->service->create(1, 'pw');
		DemoIliasState::$switches['runtime_rows'] = 'obj_members';
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Managed demo runtime rows remain in obj_members');
		$this->service->remove(1);
	}

}
