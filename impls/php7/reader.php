<?php
namespace mal\reader;

require_once 'types.php';
require_once 'exceptions.php';

use mal\MalType;
use mal\MalList;
use mal\MalInt;
use mal\MalSymbol;
use mal\exceptions\SyntaxError;

class Reader {
	private $tokens = [];
	private $cursor = 0;

	public function __construct(array $tokens) {
		$this->tokens = $tokens;
	}

	public function next(): string {
		$current_token = $this->tokens[$this->cursor];
		$this->cursor += 1;

		return $current_token;
	}

	public function peek(): string {
		return $this->tokens[$this->cursor];
	}
}

function read_str(string $input): MalType {
	$tokens = tokenize($input);
	$reader = new Reader($tokens);

	return read_form($reader);
}

function tokenize(string $input) {
	$pattern = '/[\s,]*(~@|[\[\]{}()\'`~^@]|"(?:\\\\.|[^\\\\"])*"?|;.*|[^\s\[\]{}(\'"`,;)]*)/';
	preg_match_all($pattern, $input, $matches);

	return $matches[1];
}

function read_form(Reader $reader): MalType {
	$token = $reader->peek();

	if ($token === '') {
		throw new SyntaxError("Expected ) found EOF");
	}

	if ($token === '(') {
		return read_list($reader);
	} else {
		return read_atom($reader);
	}
}

function read_list(Reader $reader): MalList {
	$list = [];

	// discard (
	$reader->next();

	while($reader->peek() !== ')') {
		$list []= read_form($reader);
	}

	// discard )
	$reader->next();

	return new MalList($list);
}

function read_atom(Reader $reader): MalType {
	$token = $reader->next();

	if (preg_match('/^\d+$/', $token)) {
		return new MalInt(intval($token));
	} else {
		return new MalSymbol($token);
	}
}
