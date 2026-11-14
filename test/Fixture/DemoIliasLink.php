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

use Base3\CoreFoundation\LinkTarget\Api\ILinkTargetService;

final class DemoIliasLink implements ILinkTargetService {

	public array $targets = [];
	public function getLink(array $target, array $params = []): string {
		$this->targets[] = [$target, $params];
		return '/ilias/demo.json?name=' . rawurlencode((string)($target['name'] ?? ''));
	}
}
