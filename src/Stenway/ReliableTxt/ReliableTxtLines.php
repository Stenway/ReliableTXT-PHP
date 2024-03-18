<?php

namespace Stenway\ReliableTxt;

abstract class ReliableTxtLines {
	static function split(string $utf8Text) : array {
		return explode("\n", $utf8Text);
	}
	
	static function join(string ...$utf8lines) : string {
		return implode("\n", $utf8lines);
	}
}
