<?php

namespace mal;

require_once 'types.php';
require_once 'reader.php';
require_once 'printer.php';

use mal\MalType;
use mal\MalList;
use mal\MalSymbol;
use mal\MalInt;
use mal\MalFunction;
use mal\exceptions\MalException;
use mal\exceptions\UnknownSymbol;

function repl_env(): array {
	return [
		'+' => new MalFunction(function($a, $b) { return new MalInt($a->unbox() + $b->unbox()); }),
		'-' => new MalFunction(function($a, $b) { return new MalInt($a->unbox() - $b->unbox()); }),
		'*' => new MalFunction( function($a, $b) { return new MalInt($a->unbox() * $b->unbox()); }),
		'/' => new MalFunction(function($a, $b) { return new MalInt(intdiv($a->unbox(), $b->unbox())); }),
	];
}

function mal_read(string $input): MalType {
	return \mal\reader\read_str($input);
}

function mal_eval(MalType $ast, array $env): MalType {
	if (!$ast instanceof MalList) {
		return eval_ast($ast, $env);
	} else if ($ast->unbox() === []) {
		return $ast;
	} else {
		$call = eval_ast($ast, $env);
		[$fun, $args] = head_tail(...$call->unbox());

		return $fun->call($args);
	}
}

function mal_print(MalType $input): string {
	return \mal\printer\pr_str($input);
}

function mal_rep(string $input): string {
	try {
		$a = mal_read($input);
		$b = mal_eval($a, repl_env());
		$c = mal_print($b);

		return $c;
	} catch (MalException $e) {
		return mal_print($e);
	}
}

function eval_ast(MalType $ast, array $env): MalType {
	if ($ast instanceof MalSymbol) {
		$val = $ast->unbox();

		if (isset($env[$val])) {
			return $env[$val];
		} else {
			throw new UnknownSymbol("Unknown symbol: {$ast->unbox()}");
		}
	} else if ($ast instanceof MalList) {
		return new MalList(array_map(function($child) use ($env) { return mal_eval($child, $env); }, $ast->unbox()));
	} else {
		return $ast;
	}
}

function mal_main(): void {
	do {
		$input = readline("user> ");

		if ($input === false) break;

		$output = mal_rep($input);
		echo $output . "\n";

	} while (true);
}

function head_tail($head, ...$tail): array {
    return [$head, $tail];
}

mal_main();
