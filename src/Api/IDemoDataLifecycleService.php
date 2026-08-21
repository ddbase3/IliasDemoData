<?php declare(strict_types=1);

namespace IliasDemoData\Api;

interface IDemoDataLifecycleService {

	public function getStatus(int $parentRefId): array;

	public function create(int $parentRefId, string $password = ''): array;

	public function remove(int $parentRefId): array;
}
