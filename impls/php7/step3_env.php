<?php

namespace mal;

require_once 'types.php';
require_once 'reader.php';
require_once 'printer.php';
require_once 'env.php';

use mal\MalType;
use mal\MalList;
use mal\MalCollection;
use mal\MalSymbol;
use mal\MalInt;
use mal\MalFunction;
use mal\exceptions\MalException;
use mal\exceptions\UnknownSymbol;
use mal\Env;

function repl_env(): Env {
	static $env = null;

	$env = $env ?? env()
		->set(sym('+'), fun(function($a, $b) { return int($a->unbox() + $b->unbox()); }))
		->set(sym('-'), fun(function($a, $b) { return int($a->unbox() - $b->unbox()); }))
		->set(sym('*'), fun(function($a, $b) { return int($a->unbox() * $b->unbox()); }))
		->set(sym('/'), fun(function($a, $b) { return int(intdiv($a->unbox(), $b->unbox())); }))
		;

	return $env;
}

function mal_read(string $input): MalType {
	return \mal\reader\read_str($input);
}

function mal_eval(MalType $ast, Env $env): MalType {
	if (!$ast instanceof MalList) {
		return eval_ast($ast, $env);
	} else if ($ast->unbox() === []) {
		return $ast;
	} else {
		return applySpecialForm($ast, $env) ?? applyFunction($ast, $env);
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

function eval_ast(MalType $ast, Env $env): MalType {
	if ($ast instanceof MalSymbol) {
		return $env->get($ast);
	} else if ($ast instanceof MalCollection) {
		$class = get_class($ast);
		return new $class(array_map(function($child) use ($env) { return mal_eval($child, $env); }, $ast->unbox()));
	} else {
		return $ast;
	}
}

const HISTORY_FILE = '.mal_php7_history';

function mal_main(): void {
	$history_path = getenv("HOME") . '/' . HISTORY_FILE;
	readline_read_history($history_path);

	do {
		$input = readline("user> ");

		if ($input === false) break;

		readline_add_history($input);
		readline_write_history($history_path);

		$output = mal_rep($input);
		echo $output . "\n";

	} while (true);
}

function head_tail($head, ...$tail): array {
    return [$head, $tail];
}

function applySpecialForm(MalList $list, Env $env): ?MalType {
	$unboxed_list = $list->unbox();

	if ($unboxed_list[0]->unbox() === 'def!') {
		$evaled = mal_eval($unboxed_list[2], $env);
		$env->set($unboxed_list[1], $evaled);
		return $evaled;
	} else if ($unboxed_list[0]->unbox() === 'let*') {
		return applyLet($unboxed_list[1], $unboxed_list[2], env($env));
	} else {
		return null;
	}
}

function applyLet(MalCollection $bindings, MalType $body, Env $env): MalType {
	do {
		[$key, $bindings] = $bindings->headTail();
		[$value, $bindings] = $bindings->headTail();
		$value = mal_eval($value, $env);

		$env->set($key, $value);
	} while (!$bindings->isEmpty());

	return mal_eval($body, $env);
}

function applyFunction(MalList $ast, Env $env) {
	$call = eval_ast($ast, $env);
	[$fun, $args] = head_tail(...$call->unbox());

	return $fun->call($args);
}

mal_main();
