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
