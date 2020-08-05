<?php
namespace mal\exceptions;

require_once 'types.php';

use mal\MalType;

class MalException extends \Exception implements MalType {
	public function toString(): string {
		return "; Error: {$this->getMessage()}";
	}
}

class SyntaxError extends MalException {
}

class UnknownSymbol extends MalException {
}
