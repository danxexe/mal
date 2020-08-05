<?php
namespace mal;

interface MalType {
	public function toString(): string;
}

class MalList implements MalType {
	private $val = null;

	public function __construct(array $val) {
		$this->val = $val;
	}

	public function toString(): string {
		$children = array_map(function($child){ return $child->toString(); }, $this->val);

		return '(' . implode(" ", $children) . ')';
	}
}

class MalInt implements MalType {
	private $val = null;

	public function __construct(int $val) {
		$this->val = $val;
	}

	public function toString(): string {
		return "{$this->val}";
	}
}

class MalSymbol implements MalType {
	private $val = null;

	public function __construct(string $val) {
		$this->val = $val;
	}

	public function toString(): string {
		return $this->val;
	}
}
