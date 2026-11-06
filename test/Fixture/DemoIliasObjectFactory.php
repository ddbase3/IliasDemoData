<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasObjectFactory {
	public static function getInstanceByObjId(int $id, bool $reference): ?DemoIliasObject {
		return (DemoIliasState::$switches['missing_orphan'] ?? false) ? null : new DemoIliasObject($id);
	}
}
