<?php
namespace mal;

interface MalType {
	public function toString(): string;
}

interface BoxedType {
	public function unbox();
}

interface MalCollection {
	public function headTail(): array;
	public function isEmpty(): bool;
}

class MalList implements MalType, BoxedType, MalCollection {
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

	public function headTail(): array {
		$arr = $this->unbox();

		return [$arr[0], lst(array_slice($arr, 1))];
	}

	public function isEmpty(): bool {
		return $this->unbox() === [];
	}
}

function lst($val) {
	return new MalList($val);
}

class MalVector implements MalType, BoxedType, MalCollection {
	private $val = null;

	public function __construct(array $val) {
		$this->val = $val;
	}

	public function toString(): string {
		$children = array_map(function($child){ return $child->toString(); }, $this->val);

		return '[' . implode(" ", $children) . ']';
	}

	public function unbox(): array {
		return $this->val;
	}

	public function headTail(): array {
		$arr = $this->unbox();

		return [$arr[0], lst(array_slice($arr, 1))];
	}

	public function isEmpty(): bool {
		return $this->unbox() === [];
	}
}

function vec($val) {
	return new MalVector($val);
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

function int(int $val) {
	return new MalInt($val);
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

function sym(string $val) {
	return new MalSymbol($val);
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

function fun(callable $val) {
	return new MalFunction($val);
}
