<?php

namespace mal;

require_once 'types.php';
require_once 'reader.php';
require_once 'printer.php';

use mal\MalType;
use mal\exceptions\MalException;

function mal_read(string $input): MalType {
	try {
		return \mal\reader\read_str($input);
	} catch (MalException $e) {
		return $e;
	}
}

function mal_eval(MalType $input): MalType {
	return $input;
}

function mal_print(MalType $input): string {
	return \mal\printer\pr_str($input);
}

function mal_rep(string $input): string {
	return mal_print(mal_eval(mal_read($input)));
}

function mal_main() {
	do {
		$input = readline("user> ");

		if ($input === false) break;

		$output = mal_rep($input);
		echo $output . "\n";

	} while (true);
}

mal_main();
