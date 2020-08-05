<?php
namespace mal;

interface MalType {
	public function toString(): string;
}

interface BoxedType {
	public function unbox();
}

class MalList implements MalType, BoxedType {
	private $val = null;

	public function __construct(array $val) {
		$this->val = $val;
	}

	public function toString(): string {
		$children = array_map(function($child){ return $child->toString(); }, $this->val);

		return '(' . implode(" ", $children) . ')';
	}

	public function unbox(): array {
		return $this->val;
	}
}

class MalInt implements MalType, BoxedType {
	private $val = null;

	public function __construct(int $val) {
		$this->val = $val;
	}

	public function toString(): string {
		return "{$this->val}";
	}

	public function unbox(): int {
		return $this->val;
	}
}

class MalSymbol implements MalType, BoxedType {
	private $val = null;

	public function __construct(string $val) {
		$this->val = $val;
	}

	public function toString(): string {
		return $this->val;
	}

	public function unbox(): string {
		return $this->val;
	}
}

class MalFunction implements MalType, BoxedType {
	private $val = null;

	public function __construct(callable $val) {
		$this->val = $val;
	}

	public function toString(): string {
		return "function";
	}

	public function unbox(): callable {
		return $this->val;
	}

	public function call($args) {
		return call_user_func_array($this->val, $args);
	}
}
