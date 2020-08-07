<?php
namespace mal;

require_once 'types.php';

use mal\MalSymbol;
use mal\MalType;
use mal\exceptions\UnknownSymbol;

class Env {
	private $outer = null;
	private $data = [];

	public function __construct(?Env $outer) {
		$this->outer = $outer;
	}

	public function set(MalSymbol $key, MalType $value): self {
		$this->data[$key->unbox()] = $value;

		return $this;
	}

	public function find(MalSymbol $key) {
		if (array_key_exists($key->unbox(), $this->data)) {
			return $this;
		}

		if ($this->outer) {
			return $this->outer->find($key);
		}

		throw new UnknownSymbol("Symbol {$key->unbox()} not found");
	}

	public function get(MalSymbol $key) {
		$env = $this->find($key);

		return $env->fetch($key);
	}

	public function fetch(MalSymbol $key) {
		return $this->data[$key->unbox()];
	}
}

function env($outer = null) {
	return new Env($outer);
}