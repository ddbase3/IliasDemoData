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

final class DemoIliasDatabase {

	public function in(string $field, array $values, bool $not, string $type): string {
		return $field . ' IN (' . implode(',', $values) . ')';
	}

	public function query(string $sql): string {
		DemoIliasState::$queries[] = $sql;
		return $sql;
	}

	public function fetchAssoc(string $query): array {
		if ((DemoIliasState::$switches['runtime_rows'] ?? '') !== ''
			&& str_contains($query, DemoIliasState::$switches['runtime_rows'])) {
			return ['cnt' => 1];
		}
		return ['cnt' => 0];
	}
}
