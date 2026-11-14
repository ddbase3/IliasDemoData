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

final class DemoIliasObject {

	public const STATUS_DETERMINATION_LP = 3;
	public const POS_FIRST_NODE = 1;

	public ?DemoIliasForumEngine $Forum = null;
	private int $id = 0;
	private int $refId = 0;
	private int $parentRefId = 0;
	private string $title = '';
	private string $description = '';
	private int $statusDetermination = 0;
	private string $resourceId = '';
	private ?DemoIliasMembers $members = null;

	public function __construct(int|self $objectId = 0, bool $withReference = true) {
		if (is_int($objectId) && $objectId > 0) {
			$this->id = $objectId;
			$this->title = DemoIliasState::$objects[$objectId]['title'] ?? '';
			$this->description = DemoIliasState::$objects[$objectId]['description'] ?? '';
			$this->refId = DemoIliasState::referencesOf($objectId)[0] ?? 0;
		}
	}

	public function setTitle(string $title): void {
		$this->title = $title;
		if (DemoIliasState::titleType($title) === 'frm') {
			$this->Forum = new DemoIliasForumEngine();
		}
	}

	public function setDescription(string $description): void { $this->description = $description; }
	public function setStartPage(string $title): void {}
	public function setOnline(bool $flag): void {}
	public function setOfflineStatus(bool $flag): void {}
	public function setStatusDetermination(int $determination): void { $this->statusDetermination = $determination; }
	public function getStatusDetermination(): int {
		return (DemoIliasState::$switches['wrong_status_determination'] ?? false) ? 0 : $this->statusDetermination;
	}
	public function setType(string $type): void {}
	public function setLMId(int $id): void {}
	public function createLMTree(): void {}
	public function getLMTree(): self { return $this; }
	public function getRootId(): int { return $this->id; }
	public function getPageObject(): DemoIliasPage { return new DemoIliasPage(); }
	public function getId(): int { return $this->id; }
	public function getRefId(): int { return $this->refId; }
	public function getTitle(): string { return $this->title; }
	public function getMembersObject(): DemoIliasMembers { return $this->members ??= new DemoIliasMembers($this->id); }

	public function create(bool $option = false): void {
		DemoIliasState::fail('object_create');
		$this->id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$objects[$this->id] = [
			'type' => DemoIliasState::titleType($this->title),
			'title' => $this->title,
			'description' => $this->description,
		];
	}

	public function createReference(): void {
		$this->refId = DemoIliasState::$nextReferenceId++;
		DemoIliasState::$references[$this->refId] = ['obj_id' => $this->id, 'parent' => 0, 'trashed' => false];
	}

	public function putInTree(int $parentRefId): void {
		$this->parentRefId = $parentRefId;
		DemoIliasState::$references[$this->refId]['parent'] = $parentRefId;
	}

	public function setPermissions(int $parentRefId): void {}
	public function update(): void {}
	public function createWikiPage(string $title): DemoIliasPage { return new DemoIliasPage(); }

	public function appendStream(string $stream, string $name): void {
		$this->resourceId = (DemoIliasState::$switches['missing_file_resource'] ?? false) ? '-' : 'resource-' . $this->id;
	}

	public function getResourceId(): string { return $this->resourceId; }

	public function delete(): void {
		DemoIliasState::fail('object_delete');
		if (DemoIliasState::$switches['retain_orphan'] ?? false) return;
		unset(DemoIliasState::$objects[$this->id]);
		foreach (DemoIliasState::referencesOf($this->id) as $ref) unset(DemoIliasState::$references[$ref]);
	}

	public static function _exists(int $id, bool $byReference = false): bool {
		return $byReference
			? isset(DemoIliasState::$references[$id])
			: isset(DemoIliasState::$objects[$id]);
	}

	public static function _getIdsForTitle(string $title, string $type): array {
		if ($type === 'wiki' && (DemoIliasState::$switches['omit_wiki_from_status'] ?? false)) return [];
		$ids = [];
		foreach (DemoIliasState::$objects as $id => $row) {
			if ($row['title'] === $title && $row['type'] === $type) $ids[] = $id;
		}
		return $ids;
	}

	public static function _lookupTitle(int $id): string {
		return DemoIliasState::$objects[$id]['title'] ?? '';
	}

	public static function _lookupDescription(int $id): string {
		return DemoIliasState::$objects[$id]['description'] ?? '';
	}

	public static function _getAllReferences(int $id): array {
		return DemoIliasState::referencesOf($id);
	}

	public static function _isInTrash(int $refId): bool {
		return DemoIliasState::$references[$refId]['trashed'] ?? false;
	}
}
