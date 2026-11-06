<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

use Base3\LinkTarget\Api\ILinkTargetService;

final class DemoIliasLink implements ILinkTargetService {

	public array $targets = [];
	public function getLink(array $target, array $params = []): string {
		$this->targets[] = [$target, $params];
		return '/ilias/demo.json?name=' . rawurlencode((string)($target['name'] ?? ''));
	}
}
