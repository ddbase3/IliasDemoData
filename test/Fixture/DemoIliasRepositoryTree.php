<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasRepositoryTree {

	public function getChilds(int $parent): array {
		$rows = [];
		foreach (DemoIliasState::$references as $ref => $link) {
			if ($link['parent'] !== $parent) continue;
			$object = DemoIliasState::$objects[$link['obj_id']] ?? null;
			if ($object === null) continue;
			$rows[] = [
				'child' => $ref, 'obj_id' => $link['obj_id'],
				'type' => $object['type'], 'title' => $object['title']
			];
		}
		return $rows;
	}
}
