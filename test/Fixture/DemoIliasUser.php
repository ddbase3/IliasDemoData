<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasUser {

	public const PASSWD_PLAIN = 1;
	private int $id = 0;
	private array $values = [];

	public function __construct(int $id = 0) {
		$this->id = $id;
		$this->values = DemoIliasState::$users[$id] ?? [];
	}

	public function create(): void { $this->id = DemoIliasState::$nextObjectId++; }
	public function saveAsNew(): void {}
	public function getId(): int { return $this->id; }
	public function getFirstname(): string { return $this->values['firstname'] ?? ''; }
	public function getLastname(): string { return $this->values['lastname'] ?? ''; }
	public function getFullname(): string { return trim($this->getFirstname() . ' ' . $this->getLastname()); }
	public function getEmail(): string { return $this->values['email'] ?? ''; }
	public function getExternalAccount(): string { return $this->values['external'] ?? ''; }
	public function setLogin(string $login): void { $this->values['login'] = $login; }
	public function setFirstname(string $value): void { $this->values['firstname'] = $value; }
	public function setLastname(string $value): void { $this->values['lastname'] = $value; }
	public function setEmail(string $value): void { $this->values['email'] = $value; }
	public function setLanguage(string $value): void { $this->values['lang'] = $value; }
	public function setAuthMode(string $value): void { $this->values['auth'] = $value; }
	public function setPasswd(string $value, int $mode): void { $this->values['password'] = $value; }
	public function setExternalAccount(string $value): void { $this->values['external'] = $value; }
	public function setLastPasswordChangeTS(int $value): void {}
	public function setActive(bool $active, int $activator): void {}
	public function setTitle(string $value): void {}
	public function setDescription(string $value): void {}
	public function update(): void {
		DemoIliasState::fail('user_update');
		DemoIliasState::$users[$this->id] = $this->values;
		DemoIliasState::$objects[$this->id] = ['type' => 'usr', 'title' => $this->values['login'], 'description' => ''];
	}
	public function delete(): void {
		if (DemoIliasState::$switches['retain_users'] ?? false) return;
		unset(DemoIliasState::$users[$this->id], DemoIliasState::$objects[$this->id]);
		foreach (DemoIliasState::$assignments as &$assigned) unset($assigned[$this->id]);
		unset($assigned);
	}

	public static function _lookupId(string $login): ?int {
		foreach (DemoIliasState::$users as $id => $user) {
			if ($user['login'] === $login) return (int)$id;
		}
		return null;
	}
}
