<?php
namespace mal\printer;

require_once 'types.php';

use mal\MalType;

function pr_str(MalType $val) {
	return $val->toString();
}
