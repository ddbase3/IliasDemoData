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

use Base3\IliasDemoData\Api\IDemoDataLifecycleService;
use RuntimeException;

final class DemoIliasLifecycle implements IDemoDataLifecycleService {

	public array $actions = [];
	public array $status = [
		'parent_valid' => true, 'is_system_admin' => true,
		'managed_roots' => [], 'managed_repository_object_ids' => [],
		'users' => [], 'role' => null, 'complete' => false,
	];
	public string $throwOn = '';
	public array $createResult = ['message' => 'created', 'generated_password' => '', 'steps' => []];
	public array $removeResult = ['message' => 'removed', 'generated_password' => '', 'steps' => []];

	public function getStatus(int $parentRefId): array {
		$this->actions[] = ['status', $parentRefId];
		if ($this->throwOn === 'status') throw new RuntimeException('status failure');
		return ['parent_ref_id' => $parentRefId] + $this->status;
	}

	public function create(int $parentRefId, string $password = ''): array {
		$this->actions[] = ['create', $parentRefId, $password];
		if ($this->throwOn === 'create') throw new RuntimeException('create failure');
		return $this->createResult;
	}

	public function remove(int $parentRefId): array {
		$this->actions[] = ['remove', $parentRefId];
		if ($this->throwOn === 'remove') throw new RuntimeException('remove failure');
		return $this->removeResult;
	}
}
