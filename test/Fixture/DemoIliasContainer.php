<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasContainer {

	public function rbac(): DemoIliasRbac {
		return new DemoIliasRbac();
	}

	public function user(): DemoIliasCurrentUser {
		return new DemoIliasCurrentUser();
	}

	public function repositoryTree(): DemoIliasRepositoryTree {
		return new DemoIliasRepositoryTree();
	}

	public function repository(): DemoIliasRepository {
		return new DemoIliasRepository();
	}

	public function database(): DemoIliasDatabase {
		return new DemoIliasDatabase();
	}
}
