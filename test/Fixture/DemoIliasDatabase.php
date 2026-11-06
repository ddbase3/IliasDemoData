<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

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
