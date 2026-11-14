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


namespace Base3\IliasDemoData\Service;

use Base3\IliasDemoData\Api\IDemoDataLifecycleService;
use Base3\IliasDemoData\Seed\DemoContentCatalog;
use ILIAS\DI\Container;
use ILIAS\Filesystem\Stream\Streams;
use RuntimeException;
use Throwable;
use ilBlogPosting;
use ilForumTopic;
use ilLMObject;
use ilLMPageObject;
use ilLPStatus;
use ilObjBlog;
use ilObjCategory;
use ilObjCourse;
use ilObjFile;
use ilObjForum;
use ilObjLearningModule;
use ilObjRole;
use ilObjUser;
use ilObjUserTracking;
use ilObjWiki;
use ilObject;
use ilObjectFactory;
use ilPageObject;
use ilParticipants;
use ilStructureObject;
use ilTree;

final class IliasDemoDataLifecycleService implements IDemoDataLifecycleService {

	private const ROOT_TITLE = 'BASE3 Demo Knowledge Lab';
	private const ROOT_DESCRIPTION = '[base3-ilias-demo-data:seed:v1] Managed demo seed subtree. Everything below this category may be deleted by the IliasDemoData plugin.';
	private const CONTENT_DESCRIPTION = '[base3-ilias-demo-data:seed:v1] Managed demo seed object.';

	private const ROLE_TITLE = 'BASE3 Demo Users';
	private const ROLE_DESCRIPTION = '[base3-ilias-demo-data:seed:v1] Managed demo seed role.';
	private const USER_EXTERNAL_ACCOUNT_PREFIX = 'base3-ilias-demo-data:seed:v1:';

	public function __construct(
		private readonly Container $dic
	) {}

	public function getStatus(int $parentRefId): array {
		$parentValid = $this->isRepositoryReference($parentRefId);
		$root = $parentValid ? $this->findRoot($parentRefId) : null;
		$managedRoots = $this->findManagedRootReferences();
		$managedRepositoryObjectIds = $this->findManagedRepositoryObjectIds();
		$users = $this->findUsers();
		$role = $this->findRole();
		$stats = DemoContentCatalog::contentStats();
		$expectedRepositoryObjects = DemoContentCatalog::expectedRepositoryObjectCount();

		$roleAssignmentsComplete = false;
		if ($role !== null && count($users) === count(DemoContentCatalog::users())) {
			$roleAssignmentsComplete = true;
			foreach ($users as $user) {
				if (!$this->dic->rbac()->review()->isAssigned((int)$user['id'], (int)$role['id'])) {
					$roleAssignmentsComplete = false;
					break;
				}
			}
		}

		$membershipStatus = $this->getMembershipStatus();
		$repositoryComplete = $root !== null
			&& count($managedRoots) === 1
			&& count($managedRepositoryObjectIds) === $expectedRepositoryObjects;

		return [
			'parent_ref_id' => $parentRefId,
			'parent_valid' => $parentValid,
			'is_system_admin' => $this->isSystemAdministrator(),
			'root' => $root,
			'managed_roots' => $managedRoots,
			'managed_repository_object_ids' => $managedRepositoryObjectIds,
			'expected_repository_object_count' => $expectedRepositoryObjects,
			'users' => $users,
			'expected_user_count' => count(DemoContentCatalog::users()),
			'role' => $role,
			'role_assignments_complete' => $roleAssignmentsComplete,
			'repository_complete' => $repositoryComplete,
			'learning_progress_enabled' => ilObjUserTracking::_enabledLearningProgress(),
			'membership_status' => $membershipStatus,
			'content_stats' => $stats,
			'complete' => $repositoryComplete
				&& count($users) === count(DemoContentCatalog::users())
				&& $role !== null
				&& $roleAssignmentsComplete
				&& !empty($membershipStatus['complete']),
		];
	}

	public function create(int $parentRefId, string $password = ''): array {
		$this->requireSystemAdministrator();
		$this->requireRepositoryParent($parentRefId);
		$this->requireCleanState($parentRefId);

		try {
			return $this->createManagedData($parentRefId, $password);
		} catch (Throwable $creationError) {
			try {
				$this->removeManagedData($parentRefId);
			} catch (Throwable $cleanupError) {
				throw new RuntimeException(
					'Demo seed creation failed: ' . $creationError->getMessage()
					. ' Cleanup of the managed partial dataset also failed: ' . $cleanupError->getMessage(),
					0,
					$creationError
				);
			}

			throw new RuntimeException(
				'Demo seed creation failed and the managed partial dataset was removed: ' . $creationError->getMessage(),
				0,
				$creationError
			);
		}
	}

	private function createManagedData(int $parentRefId, string $password): array {
		$generatedPassword = false;
		$password = trim($password);
		if ($password === '') {
			$password = 'Base3Demo-' . bin2hex(random_bytes(8)) . '!';
			$generatedPassword = true;
		}

		$steps = [];
		$root = $this->createCategory(self::ROOT_TITLE, self::ROOT_DESCRIPTION, $parentRefId);
		$rootRefId = (int)$root->getRefId();
		$steps[] = $this->repositoryStep('Demo root category', $root);

		$categoryRefs = [];
		foreach (DemoContentCatalog::categories() as $definition) {
			$category = $this->createCategory(
				(string)$definition['title'],
				self::CONTENT_DESCRIPTION,
				$rootRefId
			);
			$categoryRefs[(string)$definition['key']] = (int)$category->getRefId();
		}
		$steps[] = [
			'name' => 'Repository categories',
			'id' => count($categoryRefs),
			'ref_id' => null,
			'detail' => count($categoryRefs) . ' managed categories created',
		];

		$role = $this->createRole();
		$roleId = (int)$role->getId();
		$steps[] = [
			'name' => 'Global demo role',
			'id' => $roleId,
			'ref_id' => null,
			'detail' => self::ROLE_TITLE,
		];

		$userIds = [];
		foreach (DemoContentCatalog::users() as $definition) {
			$user = $this->createUser($definition, $password);
			$userId = (int)$user->getId();
			$userIds[(string)$definition['login']] = $userId;
			$this->dic->rbac()->admin()->assignUser($roleId, $userId);
			if (!$this->dic->rbac()->review()->isAssigned($userId, $roleId)) {
				throw new RuntimeException('Demo user ' . $definition['login'] . ' could not be assigned to the demo role.');
			}
		}
		$steps[] = [
			'name' => 'Demo users',
			'id' => count($userIds),
			'ref_id' => null,
			'detail' => count($userIds) . ' users created and assigned to ' . self::ROLE_TITLE,
		];

		$courseObjects = [];
		foreach (DemoContentCatalog::courses() as $definition) {
			$course = $this->createCourse($definition, $categoryRefs['training']);
			$courseObjects[(string)$definition['key']] = $course;
		}
		$steps[] = [
			'name' => 'Demo courses',
			'id' => count($courseObjects),
			'ref_id' => null,
			'detail' => count($courseObjects) . ' courses created',
		];

		$membershipCount = $this->seedCourseMemberships($courseObjects, $userIds);
		$steps[] = [
			'name' => 'Course memberships and progress',
			'id' => $membershipCount,
			'ref_id' => null,
			'detail' => $membershipCount . ' course assignments seeded with mixed participation states',
		];

		$wikiPageCount = 0;
		foreach (DemoContentCatalog::wikis() as $definition) {
			$wikiPageCount += $this->createWiki($definition, $categoryRefs[(string)$definition['parent']]);
		}
		$steps[] = [
			'name' => 'Wiki knowledge',
			'id' => $wikiPageCount,
			'ref_id' => null,
			'detail' => $wikiPageCount . ' wiki pages created',
		];

		$blogPostCount = 0;
		$authorLogins = array_keys($userIds);
		$authorIndex = 0;
		foreach (DemoContentCatalog::blogs() as $definition) {
			$blogPostCount += $this->createBlog(
				$definition,
				$categoryRefs[(string)$definition['parent']],
				$userIds,
				$authorLogins,
				$authorIndex
			);
		}
		$steps[] = [
			'name' => 'Blog knowledge',
			'id' => $blogPostCount,
			'ref_id' => null,
			'detail' => $blogPostCount . ' blog posts created',
		];

		$forumThreadCount = 0;
		foreach (DemoContentCatalog::forums() as $definition) {
			$course = $courseObjects[(string)$definition['course']] ?? null;
			if (!$course instanceof ilObjCourse) {
				throw new RuntimeException('Parent course for forum ' . $definition['title'] . ' is missing.');
			}
			$forumThreadCount += $this->createForum(
				$definition,
				(int)$course->getRefId(),
				$userIds,
				$authorLogins,
				$authorIndex
			);
		}
		$steps[] = [
			'name' => 'Course forums',
			'id' => $forumThreadCount,
			'ref_id' => null,
			'detail' => $forumThreadCount . ' forum threads created inside the demo courses',
		];

		$learningPageCount = 0;
		foreach (DemoContentCatalog::learningModules() as $definition) {
			$course = $courseObjects[(string)$definition['course']] ?? null;
			if (!$course instanceof ilObjCourse) {
				throw new RuntimeException('Parent course for learning module ' . $definition['title'] . ' is missing.');
			}
			$learningPageCount += $this->createLearningModule($definition, (int)$course->getRefId());
		}
		$steps[] = [
			'name' => 'Learning modules',
			'id' => $learningPageCount,
			'ref_id' => null,
			'detail' => $learningPageCount . ' learning-module pages created inside the demo courses',
		];

		$fileCount = 0;
		foreach (DemoContentCatalog::files() as $definition) {
			$this->createFile($definition, $categoryRefs[(string)$definition['parent']]);
			$fileCount++;
		}
		$steps[] = [
			'name' => 'Reference files',
			'id' => $fileCount,
			'ref_id' => null,
			'detail' => $fileCount . ' searchable text files created',
		];

		$status = $this->getStatus($parentRefId);
		if (!$status['complete']) {
			throw new RuntimeException('Demo seed creation finished, but the resulting managed dataset is incomplete.');
		}

		$stats = DemoContentCatalog::contentStats();
		$steps[] = [
			'name' => 'RAG corpus',
			'id' => (int)$stats['approx_words'],
			'ref_id' => null,
			'detail' => 'Approximately ' . number_format((int)$stats['approx_words']) . ' English words of managed demo knowledge',
		];

		return [
			'message' => 'Full BASE3 ILIAS demo dataset was created successfully.',
			'steps' => $steps,
			'generated_password' => $generatedPassword ? $password : '',
		];
	}

	public function remove(int $parentRefId): array {
		$this->requireSystemAdministrator();
		$this->requireRepositoryParent($parentRefId);

		return $this->removeManagedData($parentRefId);
	}

	private function removeManagedData(int $parentRefId): array {
		$this->assertRemovalOwnership($parentRefId);

		$steps = [];
		$managedRoots = $this->findManagedRootReferences();
		$managedRepositoryObjectIds = $this->findManagedRepositoryObjectIds();
		$users = $this->findUsers();
		$role = $this->findRole();
		$courseObjectIds = [];
		foreach (DemoContentCatalog::courses() as $definition) {
			$courseId = $this->findManagedObjectId('crs', (string)$definition['title']);
			if ($courseId > 0) {
				$courseObjectIds[] = $courseId;
			}
		}
		$userIds = array_map(static fn(array $user): int => (int)$user['id'], $users);
		$deletion = $this->dic->repository()->internal()->domain()->deletion();

		foreach ($managedRoots as $managedRoot) {
			$rootRefId = (int)$managedRoot['ref_id'];
			if (!ilObject::_exists($rootRefId, true)) {
				continue;
			}

			if (!ilObject::_isInTrash($rootRefId)) {
				$deletion->deleteObjectsByRefIds([$rootRefId]);
			}

			if (ilObject::_exists($rootRefId, true)) {
				$deletion->removeObjectsFromSystemByRefIds([$rootRefId]);
			}

			if (ilObject::_exists($rootRefId, true)) {
				throw new RuntimeException('The demo repository root ' . $rootRefId . ' still exists after deletion.');
			}

			$steps[] = [
				'name' => 'Repository demo subtree',
				'id' => (int)$managedRoot['obj_id'],
				'ref_id' => $rootRefId,
				'detail' => 'Removed and verified',
			];
		}

		foreach ($managedRepositoryObjectIds as $objId) {
			$objId = (int)$objId;
			if ($objId <= 0 || !ilObject::_exists($objId)) {
				continue;
			}

			foreach (ilObject::_getAllReferences($objId) as $refId) {
				$refId = (int)$refId;
				if ($refId <= 0 || !ilObject::_exists($refId, true)) {
					continue;
				}
				if (!ilObject::_isInTrash($refId)) {
					$deletion->deleteObjectsByRefIds([$refId]);
				}
				if (ilObject::_exists($refId, true)) {
					$deletion->removeObjectsFromSystemByRefIds([$refId]);
				}
			}

			if (ilObject::_exists($objId)) {
				$object = ilObjectFactory::getInstanceByObjId($objId, false);
				if ($object === null) {
					throw new RuntimeException('Managed repository object ' . $objId . ' could not be loaded for orphan cleanup.');
				}
				$object->delete();
			}

			if (ilObject::_exists($objId)) {
				throw new RuntimeException('Managed repository object ' . $objId . ' still exists after lifecycle deletion.');
			}
		}

		$deletedUsers = 0;
		foreach ($users as $user) {
			$userObject = new ilObjUser((int)$user['id']);
			$userObject->delete();
			if (ilObjUser::_lookupId((string)$user['login']) !== null || ilObject::_exists((int)$user['id'])) {
				throw new RuntimeException('Demo user ' . $user['login'] . ' still exists after deletion.');
			}
			$deletedUsers++;
		}
		if ($deletedUsers > 0) {
			$steps[] = [
				'name' => 'Demo users',
				'id' => $deletedUsers,
				'ref_id' => null,
				'detail' => $deletedUsers . ' users removed and verified',
			];
		}

		if ($role !== null) {
			$assignedUsers = $this->dic->rbac()->review()->assignedUsers((int)$role['id']);
			if ($assignedUsers !== []) {
				throw new RuntimeException('The demo role still has assigned users and will not be deleted.');
			}
			$roleObject = new ilObjRole((int)$role['id']);
			$roleObject->setParent(ROLE_FOLDER_ID);
			if (!$roleObject->delete()) {
				throw new RuntimeException('The demo role could not be deleted through ilObjRole::delete().');
			}
			if ($this->findRoleByTitle() !== null || ilObject::_exists((int)$role['id'])) {
				throw new RuntimeException('The demo role still exists after deletion.');
			}
			$steps[] = [
				'name' => 'Global demo role',
				'id' => (int)$role['id'],
				'ref_id' => null,
				'detail' => 'Removed and verified',
			];
		}

		$this->verifyNoDemoRuntimeRows($courseObjectIds, $userIds);

		$status = $this->getStatus($parentRefId);
		if (
			$status['managed_roots'] !== []
			|| $status['managed_repository_object_ids'] !== []
			|| $status['users'] !== []
			|| $status['role'] !== null
		) {
			throw new RuntimeException('Managed demo data remains after the remove operation.');
		}

		return [
			'message' => $steps === []
				? 'No managed BASE3 ILIAS demo data was found.'
				: 'Full BASE3 ILIAS demo dataset was removed successfully and verified as deleted.',
			'steps' => $steps,
			'generated_password' => '',
		];
	}

	private function createCategory(string $title, string $description, int $parentRefId): ilObjCategory {
		$category = new ilObjCategory();
		$category->setTitle($title);
		$category->setDescription($description);
		$category->create();
		$category->createReference();
		$category->putInTree($parentRefId);
		$category->setPermissions($parentRefId);
		$category->update();
		return $category;
	}

	private function createRole(): ilObjRole {
		$role = new ilObjRole();
		$role->setTitle(self::ROLE_TITLE);
		$role->setDescription(self::ROLE_DESCRIPTION);
		$role->setAllowRegister(false);
		$role->toggleAssignUsersStatus(true);
		$role->create();
		$this->dic->rbac()->admin()->assignRoleToFolder($role->getId(), ROLE_FOLDER_ID, 'y');
		return $role;
	}

	private function createUser(array $definition, string $password): ilObjUser {
		$user = new ilObjUser();
		$user->create();
		$user->saveAsNew();
		$user->setLogin((string)$definition['login']);
		$user->setFirstname((string)$definition['firstname']);
		$user->setLastname((string)$definition['lastname']);
		$user->setEmail((string)$definition['email']);
		$user->setLanguage('en');
		$user->setAuthMode('default');
		$user->setPasswd($password, ilObjUser::PASSWD_PLAIN);
		$user->setExternalAccount(self::USER_EXTERNAL_ACCOUNT_PREFIX . $definition['login']);
		$user->setLastPasswordChangeTS(time());
		$user->setActive(true, $this->dic->user()->getId());
		$user->setTitle($user->getFullname());
		$user->setDescription($user->getEmail());
		$user->update();
		return $user;
	}

	private function createCourse(array $definition, int $parentRefId): ilObjCourse {
		$course = new ilObjCourse();
		$course->setTitle((string)$definition['title']);
		$course->setDescription(self::CONTENT_DESCRIPTION);
		$course->create();
		$course->createReference();
		$course->putInTree($parentRefId);
		$course->setPermissions($parentRefId);
		$course->setOfflineStatus(false);
		$course->setStatusDetermination(ilObjCourse::STATUS_DETERMINATION_LP);
		$course->update();
		return $course;
	}

	private function seedCourseMemberships(array $courseObjects, array $userIds): int {
		$count = 0;
		$lpEnabled = ilObjUserTracking::_enabledLearningProgress();
		$statusMap = [
			'not_attempted' => ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM,
			'in_progress' => ilLPStatus::LP_STATUS_IN_PROGRESS_NUM,
			'completed' => ilLPStatus::LP_STATUS_COMPLETED_NUM,
			'failed' => ilLPStatus::LP_STATUS_FAILED_NUM,
		];

		foreach (DemoContentCatalog::courses() as $definition) {
			$course = $courseObjects[(string)$definition['key']] ?? null;
			if (!$course instanceof ilObjCourse) {
				throw new RuntimeException('Course ' . $definition['title'] . ' is missing while seeding memberships.');
			}
			if ($lpEnabled && $course->getStatusDetermination() !== ilObjCourse::STATUS_DETERMINATION_LP) {
				throw new RuntimeException('Learning progress is enabled, but course ' . $definition['title'] . ' does not use learning-progress status determination.');
			}
			foreach ((array)$definition['memberships'] as $login => $membership) {
				$userId = $userIds[$login] ?? 0;
				if ($userId <= 0) {
					throw new RuntimeException('Demo user ' . $login . ' is missing while seeding course memberships.');
				}
				$role = (string)$membership['role'] === 'tutor'
					? ilParticipants::IL_CRS_TUTOR
					: ilParticipants::IL_CRS_MEMBER;
				if (!$course->getMembersObject()->add($userId, $role)) {
					throw new RuntimeException('Demo user ' . $login . ' could not be added to course ' . $definition['title'] . '.');
				}
				if (!$course->getMembersObject()->isAssigned($userId)) {
					throw new RuntimeException('Course assignment for ' . $login . ' in ' . $definition['title'] . ' could not be verified.');
				}

				if ($role === ilParticipants::IL_CRS_MEMBER) {
					$statusName = (string)$membership['status'];
					$percentage = (int)$membership['percentage'];
					if ($lpEnabled) {
						$status = $statusMap[$statusName] ?? null;
						if (!is_int($status)) {
							throw new RuntimeException('Unknown learning-progress status ' . $statusName . '.');
						}
						ilLPStatus::writeStatus($course->getId(), $userId, $status, $percentage, true);
						if (
							ilLPStatus::_lookupStatus($course->getId(), $userId, false) !== $status
							|| (int)ilLPStatus::_lookupPercentage($course->getId(), $userId) !== $percentage
						) {
							throw new RuntimeException('Learning progress for ' . $login . ' in ' . $definition['title'] . ' could not be verified after writing.');
						}
					} else {
						$course->getMembersObject()->updatePassed($userId, $statusName === 'completed', false, true);
					}
				}
				$count++;
			}
		}

		return $count;
	}

	private function createWiki(array $definition, int $parentRefId): int {
		$pages = (array)$definition['pages'];
		if ($pages === []) {
			throw new RuntimeException('Wiki ' . $definition['title'] . ' has no pages.');
		}
		$wiki = new ilObjWiki();
		$wiki->setTitle((string)$definition['title']);
		$wiki->setDescription(self::CONTENT_DESCRIPTION);
		$wiki->setStartPage((string)$pages[0]['title']);
		$wiki->setOnline(true);
		$wiki->create(true);
		$wiki->createReference();
		$wiki->putInTree($parentRefId);
		$wiki->setPermissions($parentRefId);
		$wiki->update();

		$count = 0;
		foreach ($pages as $pageDefinition) {
			$page = $wiki->createWikiPage((string)$pageDefinition['title']);
			$this->writePageText($page, (string)$pageDefinition['text']);
			$count++;
		}
		return $count;
	}

	private function createBlog(
		array $definition,
		int $parentRefId,
		array $userIds,
		array $authorLogins,
		int &$authorIndex
	): int {
		$blog = new ilObjBlog();
		$blog->setTitle((string)$definition['title']);
		$blog->setDescription(self::CONTENT_DESCRIPTION);
		$blog->create();
		$blog->createReference();
		$blog->putInTree($parentRefId);
		$blog->setPermissions($parentRefId);
		$blog->update();

		$count = 0;
		foreach ((array)$definition['posts'] as $postDefinition) {
			$login = $authorLogins[$authorIndex % count($authorLogins)];
			$authorIndex++;
			$post = new ilBlogPosting();
			$post->setTitle((string)$postDefinition['title']);
			$post->setBlogId($blog->getId());
			$post->setAuthor((int)$userIds[$login]);
			$post->setActive(false);
			$post->create(false);
			$post = new ilBlogPosting($post->getId());
			$this->writePageText($post, (string)$postDefinition['text']);
			$post->setApproved(true);
			$post->setActive(true);
			$post->update(true, false, false);
			$count++;
		}
		return $count;
	}

	private function createForum(
		array $definition,
		int $parentRefId,
		array $userIds,
		array $authorLogins,
		int &$authorIndex
	): int {
		$forum = new ilObjForum();
		$forum->setTitle((string)$definition['title']);
		$forum->setDescription(self::CONTENT_DESCRIPTION);
		$forum->create();
		$forum->createReference();
		$forum->putInTree($parentRefId);
		$forum->setPermissions($parentRefId);
		$forum->setOfflineStatus(false);
		$forum->update();

		$forumEngine = $forum->Forum;
		$forumEngine->setForumId($forum->getId());
		$forumEngine->setForumRefId($forum->getRefId());
		$forumEngine->setMDB2WhereCondition('top_frm_fk = %s ', ['integer'], [$forum->getId()]);
		$topicData = $forumEngine->getOneTopic();
		if ($topicData->getTopPk() <= 0) {
			throw new RuntimeException('Forum topic data for ' . $definition['title'] . ' could not be initialized.');
		}

		$count = 0;
		foreach ((array)$definition['threads'] as $threadDefinition) {
			$login = $authorLogins[$authorIndex % count($authorLogins)];
			$authorIndex++;
			$thread = new ilForumTopic(0, true, true);
			$thread->setForumId($topicData->getTopPk());
			$thread->setThrAuthorId((int)$userIds[$login]);
			$thread->setDisplayUserId((int)$userIds[$login]);
			$thread->setSubject((string)$threadDefinition['title']);
			$thread->setUserAlias('');
			$postId = $forumEngine->generateThread($thread, (string)$threadDefinition['text'], false, false, true);
			if ($postId <= 0) {
				throw new RuntimeException('Forum thread ' . $threadDefinition['title'] . ' could not be created.');
			}
			$count++;
		}
		return $count;
	}

	private function createLearningModule(array $definition, int $parentRefId): int {
		$learningModule = new ilObjLearningModule();
		$learningModule->setTitle((string)$definition['title']);
		$learningModule->setDescription(self::CONTENT_DESCRIPTION);
		$learningModule->create();
		$learningModule->createReference();
		$learningModule->putInTree($parentRefId);
		$learningModule->setPermissions($parentRefId);
		$learningModule->createLMTree();
		$learningModule->setOfflineStatus(false);
		$learningModule->update();

		$chapter = new ilStructureObject($learningModule);
		$chapter->setType('st');
		$chapter->setTitle((string)$definition['title'] . ' Content');
		$chapter->setLMId($learningModule->getId());
		$chapter->create();
		ilLMObject::putInTree($chapter, $learningModule->getLMTree()->getRootId(), ilTree::POS_FIRST_NODE);

		$count = 0;
		foreach ((array)$definition['pages'] as $pageDefinition) {
			$page = new ilLMPageObject($learningModule);
			$page->setType('pg');
			$page->setTitle((string)$pageDefinition['title']);
			$page->setLMId($learningModule->getId());
			$page->create();
			ilLMObject::putInTree($page, $chapter->getId(), ilTree::POS_FIRST_NODE);
			$this->writePageText($page->getPageObject(), (string)$pageDefinition['text']);
			$count++;
		}
		return $count;
	}

	private function createFile(array $definition, int $parentRefId): ilObjFile {
		$file = new ilObjFile();
		$file->setTitle((string)$definition['name']);
		$file->setDescription(self::CONTENT_DESCRIPTION);
		$file->create();
		$file->createReference();
		$file->putInTree($parentRefId);
		$file->setPermissions($parentRefId);
		$file->appendStream(Streams::ofString((string)$definition['text'] . "\n"), (string)$definition['name']);
		$file->update();
		if ($file->getResourceId() === '' || $file->getResourceId() === '-') {
			throw new RuntimeException('Demo file ' . $definition['name'] . ' has no ResourceStorage resource id.');
		}
		return $file;
	}

	private function getMembershipStatus(): array {
		$expected = 0;
		$assigned = 0;
		$progressVerified = 0;
		$lpEnabled = ilObjUserTracking::_enabledLearningProgress();
		$statusMap = [
			'not_attempted' => ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM,
			'in_progress' => ilLPStatus::LP_STATUS_IN_PROGRESS_NUM,
			'completed' => ilLPStatus::LP_STATUS_COMPLETED_NUM,
			'failed' => ilLPStatus::LP_STATUS_FAILED_NUM,
		];
		$users = [];
		foreach ($this->findUsers() as $user) {
			$users[(string)$user['login']] = (int)$user['id'];
		}

		foreach (DemoContentCatalog::courses() as $definition) {
			$courseId = $this->findManagedObjectId('crs', (string)$definition['title']);
			if ($courseId <= 0) {
				$expected += count((array)$definition['memberships']);
				continue;
			}
			$course = new ilObjCourse($courseId, false);
			foreach ((array)$definition['memberships'] as $login => $membership) {
				$expected++;
				$userId = $users[$login] ?? 0;
				if ($userId <= 0 || !$course->getMembersObject()->isAssigned($userId)) {
					continue;
				}
				$assigned++;
				if ((string)$membership['role'] === 'tutor' || !$lpEnabled) {
					$progressVerified++;
					continue;
				}
				$expectedStatus = $statusMap[(string)$membership['status']] ?? null;
				$actualStatus = ilLPStatus::_lookupStatus($courseId, $userId, false);
				$actualPercentage = ilLPStatus::_lookupPercentage($courseId, $userId);
				if ($actualStatus === $expectedStatus && (int)$actualPercentage === (int)$membership['percentage']) {
					$progressVerified++;
				}
			}
		}

		return [
			'expected' => $expected,
			'assigned' => $assigned,
			'progress_verified' => $progressVerified,
			'complete' => $expected > 0 && $assigned === $expected && $progressVerified === $expected,
		];
	}


	private function verifyNoDemoRuntimeRows(array $courseObjectIds, array $userIds): void {
		if ($courseObjectIds === [] && $userIds === []) {
			return;
		}

		$db = $this->dic->database();
		$conditions = [];
		if ($courseObjectIds !== []) {
			$conditions[] = $db->in('obj_id', $courseObjectIds, false, 'integer');
		}
		if ($userIds !== []) {
			$conditions[] = $db->in('usr_id', $userIds, false, 'integer');
		}
		$where = implode(' OR ', $conditions);

		foreach (['obj_members', 'ut_lp_marks'] as $table) {
			$result = $db->query('SELECT COUNT(*) cnt FROM ' . $table . ' WHERE ' . $where);
			$row = $db->fetchAssoc($result);
			if ((int)($row['cnt'] ?? 0) !== 0) {
				throw new RuntimeException('Managed demo runtime rows remain in ' . $table . ' after lifecycle deletion.');
			}
		}
	}

	private function requireSystemAdministrator(): void {
		if (!$this->isSystemAdministrator()) {
			throw new RuntimeException('Only an ILIAS system administrator may create or remove demo seed data.');
		}
	}

	private function isSystemAdministrator(): bool {
		return $this->dic->rbac()->review()->isAssigned($this->dic->user()->getId(), SYSTEM_ROLE_ID);
	}

	private function requireRepositoryParent(int $parentRefId): void {
		if (!$this->isRepositoryReference($parentRefId)) {
			throw new RuntimeException('The selected parent_ref_id is not an existing ILIAS repository reference.');
		}
	}

	private function isRepositoryReference(int $refId): bool {
		return $refId > 0 && ilObject::_exists($refId, true);
	}

	private function requireCleanState(int $parentRefId): void {
		if (
			$this->findManagedRootReferences() !== []
			|| $this->findManagedRepositoryObjectIds() !== []
			|| $this->findUsers() !== []
			|| $this->findRole() !== null
		) {
			throw new RuntimeException('Managed BASE3 ILIAS demo data already exists. Remove it before creating a new dataset.');
		}

		if ($this->findRootTitleConflict($parentRefId) !== null) {
			throw new RuntimeException('A category with the demo root title already exists without the expected ownership marker.');
		}
		$this->assertUserOwnershipConflicts();
		$role = $this->findRoleByTitle();
		if ($role !== null) {
			throw new RuntimeException('The demo role title already exists without the expected ownership marker.');
		}
	}

	private function assertRemovalOwnership(int $parentRefId): void {
		$managedRoots = $this->findManagedRootReferences();
		if ($managedRoots === [] && $this->findRootTitleConflict($parentRefId) !== null) {
			throw new RuntimeException('A category with the demo root title exists but does not carry the expected ownership marker. It will not be deleted.');
		}
		$this->assertUserOwnershipConflicts();
		$role = $this->findRoleByTitle();
		if ($role !== null && (string)$role['description'] !== self::ROLE_DESCRIPTION) {
			throw new RuntimeException('The demo role title exists but its ownership marker does not match. The role will not be deleted.');
		}
	}

	private function assertUserOwnershipConflicts(): void {
		foreach (DemoContentCatalog::users() as $definition) {
			$login = (string)$definition['login'];
			$userId = ilObjUser::_lookupId($login);
			if (!is_int($userId) || $userId <= 0) {
				continue;
			}
			$user = new ilObjUser($userId);
			if ($user->getExternalAccount() !== self::USER_EXTERNAL_ACCOUNT_PREFIX . $login) {
				throw new RuntimeException('Demo login ' . $login . ' exists but its ownership marker does not match. It will not be modified or deleted.');
			}
		}
	}

	private function findManagedRootReferences(): array {
		$roots = [];
		foreach (ilObject::_getIdsForTitle(self::ROOT_TITLE, 'cat') as $objId) {
			$objId = (int)$objId;
			if ($objId <= 0 || (string)ilObject::_lookupDescription($objId) !== self::ROOT_DESCRIPTION) {
				continue;
			}
			foreach (ilObject::_getAllReferences($objId) as $refId) {
				$refId = (int)$refId;
				if ($refId <= 0 || !ilObject::_exists($refId, true)) {
					continue;
				}
				$roots[] = [
					'ref_id' => $refId,
					'obj_id' => $objId,
					'title' => self::ROOT_TITLE,
					'trashed' => ilObject::_isInTrash($refId),
				];
			}
		}
		usort($roots, static fn(array $left, array $right): int => (int)$left['ref_id'] <=> (int)$right['ref_id']);
		return $roots;
	}

	private function findManagedRepositoryObjectIds(): array {
		$ids = [];
		foreach (DemoContentCatalog::repositoryDefinitions(self::ROOT_TITLE) as $definition) {
			$type = (string)$definition['type'];
			$title = (string)$definition['title'];
			$expectedDescription = $type === 'cat' && $title === self::ROOT_TITLE
				? self::ROOT_DESCRIPTION
				: self::CONTENT_DESCRIPTION;
			foreach (ilObject::_getIdsForTitle($title, $type) as $objId) {
				$objId = (int)$objId;
				if ($objId > 0 && (string)ilObject::_lookupDescription($objId) === $expectedDescription) {
					$ids[$objId] = $objId;
				}
			}
		}
		return array_values($ids);
	}

	private function findManagedObjectId(string $type, string $title): int {
		foreach (ilObject::_getIdsForTitle($title, $type) as $objId) {
			$objId = (int)$objId;
			if ($objId > 0 && (string)ilObject::_lookupDescription($objId) === self::CONTENT_DESCRIPTION) {
				return $objId;
			}
		}
		return 0;
	}

	private function findRoot(int $parentRefId): ?array {
		foreach ($this->dic->repositoryTree()->getChilds($parentRefId) as $node) {
			$objId = (int)($node['obj_id'] ?? 0);
			if (
				(string)($node['type'] ?? '') === 'cat'
				&& (string)($node['title'] ?? '') === self::ROOT_TITLE
				&& $objId > 0
				&& (string)ilObject::_lookupDescription($objId) === self::ROOT_DESCRIPTION
			) {
				return [
					'ref_id' => (int)$node['child'],
					'obj_id' => $objId,
					'title' => (string)$node['title'],
				];
			}
		}
		return null;
	}

	private function findRootTitleConflict(int $parentRefId): ?array {
		foreach ($this->dic->repositoryTree()->getChilds($parentRefId) as $node) {
			$objId = (int)($node['obj_id'] ?? 0);
			if (
				(string)($node['type'] ?? '') === 'cat'
				&& (string)($node['title'] ?? '') === self::ROOT_TITLE
				&& ($objId <= 0 || (string)ilObject::_lookupDescription($objId) !== self::ROOT_DESCRIPTION)
			) {
				return $node;
			}
		}
		return null;
	}

	private function findUsers(): array {
		$users = [];
		foreach (DemoContentCatalog::users() as $definition) {
			$login = (string)$definition['login'];
			$userId = ilObjUser::_lookupId($login);
			if (!is_int($userId) || $userId <= 0) {
				continue;
			}
			$user = new ilObjUser($userId);
			if ($user->getExternalAccount() !== self::USER_EXTERNAL_ACCOUNT_PREFIX . $login) {
				continue;
			}
			$users[] = [
				'id' => $userId,
				'login' => $login,
				'name' => trim($user->getFirstname() . ' ' . $user->getLastname()),
				'external_account' => $user->getExternalAccount(),
			];
		}
		return $users;
	}

	private function findRole(): ?array {
		$role = $this->findRoleByTitle();
		if ($role === null || (string)$role['description'] !== self::ROLE_DESCRIPTION) {
			return null;
		}
		return $role;
	}

	private function findRoleByTitle(): ?array {
		foreach ($this->dic->rbac()->review()->getGlobalRoles() as $roleId) {
			if (ilObject::_lookupTitle($roleId) === self::ROLE_TITLE) {
				return [
					'id' => (int)$roleId,
					'title' => self::ROLE_TITLE,
					'description' => (string)ilObject::_lookupDescription($roleId),
				];
			}
		}
		return null;
	}

	private function writePageText(ilPageObject $page, string $text): void {
		$xml = '<PageObject><PageContent><Paragraph Language="en">'
			. htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8')
			. '</Paragraph></PageContent></PageObject>';
		$page->setXMLContent($xml);
		if (!$page->updateFromXML()) {
			throw new RuntimeException('ILIAS rejected managed demo page XML content.');
		}
	}

	private function repositoryStep(string $name, object $object, string $detail = ''): array {
		return [
			'name' => $name,
			'id' => (int)$object->getId(),
			'ref_id' => (int)$object->getRefId(),
			'detail' => $detail !== '' ? $detail : (string)$object->getTitle(),
		];
	}
}
