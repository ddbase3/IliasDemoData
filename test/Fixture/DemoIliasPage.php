<?php declare(strict_types=1);

namespace IliasDemoData\Test\Fixture;

final class DemoIliasPage {

	private int $id;
	private string $xml = '';

	public function __construct(int $id = 0) { $this->id = $id; }
	public function setXMLContent(string $xml): void { $this->xml = $xml; }
	public function updateFromXML(): bool {
		DemoIliasState::$switches['last_xml'] = $this->xml;
		return !(DemoIliasState::$switches['reject_xml'] ?? false);
	}
	public function setTitle(string $value): void {}
	public function setBlogId(int $id): void {}
	public function setAuthor(int $id): void {}
	public function setActive(bool $value): void {}
	public function setApproved(bool $value): void {}
	public function update(bool $a, bool $b, bool $c): void {}
	public function create(bool $flag): void { $this->id = DemoIliasState::$nextPostId++; }
	public function getId(): int { return $this->id; }
}
