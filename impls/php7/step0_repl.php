<?php

namespace mal;

function mal_read(string $input): string {
	return $input;
}

function mal_eval(string $input): string {
	return $input;
}

function mal_print(string $input): string {
	return $input;
}

function mal_rep(string $input): string {
	return mal_print(mal_eval(mal_read($input)));
}

function mal_main() {
	$stdin = fopen('php://stdin', 'r');

	do {
		echo "user> ";
		$input = fgets($stdin);
		$output = mal_rep($input);
		echo $output;
	} while (!feof($stdin));
}

mal_main();
