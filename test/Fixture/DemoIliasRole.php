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

final class DemoIliasRole {

	private int $id;
	private string $title = '';
	private string $description = '';

	public function __construct(int $id = 0) { $this->id = $id; }
	public function setTitle(string $title): void { $this->title = $title; }
	public function setDescription(string $description): void { $this->description = $description; }
	public function setAllowRegister(bool $flag): void {}
	public function toggleAssignUsersStatus(bool $flag): void {}
	public function getId(): int { return $this->id; }
	public function setParent(int $id): void {}
	public function create(): void {
		$this->id = DemoIliasState::$nextObjectId++;
		DemoIliasState::$roles[$this->id] = true;
		DemoIliasState::$objects[$this->id] = ['type' => 'role', 'title' => $this->title, 'description' => $this->description];
	}
	public function delete(): bool {
		if (DemoIliasState::$switches['role_delete_failure'] ?? false) return false;
		if (DemoIliasState::$switches['retain_role_after_delete'] ?? false) return true;
		unset(DemoIliasState::$roles[$this->id], DemoIliasState::$objects[$this->id]);
		return true;
	}
}
