<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasRepository {

	public function internal(): self { return $this; }
	public function domain(): self { return $this; }
	public function deletion(): self { return $this; }

	public function deleteObjectsByRefIds(array $refIds): void {
		foreach ($refIds as $refId) {
			DemoIliasState::$deletedReferences[] = $refId;
			if (isset(DemoIliasState::$references[$refId])) {
				DemoIliasState::$references[$refId]['trashed'] = true;
			}
		}
	}

	public function removeObjectsFromSystemByRefIds(array $refIds): void {
		if (DemoIliasState::$switches['retain_references'] ?? false) return;
		foreach ($refIds as $refId) {
			$this->removeSubtree((int)$refId);
		}
	}

	private function removeSubtree(int $refId): void {
		foreach (DemoIliasState::$references as $child => $record) {
			if ($record['parent'] === $refId) $this->removeSubtree((int)$child);
		}
		$id = DemoIliasState::$references[$refId]['obj_id'] ?? 0;
		unset(DemoIliasState::$references[$refId]);
		if (DemoIliasState::referencesOf($id) === []) unset(DemoIliasState::$objects[$id]);
	}
}
