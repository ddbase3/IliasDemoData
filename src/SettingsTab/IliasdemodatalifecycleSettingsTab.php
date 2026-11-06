<?php declare(strict_types=1);

namespace IliasDemoData\SettingsTab;

use UiFoundation\Settings\AbstractAdministrationSettingsTab;

final class IliasdemodatalifecycleSettingsTab extends AbstractAdministrationSettingsTab {

	public function getParentTabName(): ?string {
		return 'base3settingstab';
	}

	public function getDisplayName(): ?string {
		return 'iliasdemodatalifecycletestdisplay';
	}

	public function getOrder(): int {
		return 110;
	}
}
