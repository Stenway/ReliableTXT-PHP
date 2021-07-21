<?php

namespace Stenway\ReliableTxt;

abstract class ReliableTxtEncoder {
	static function encode(string $utf8Text, int $encoding) : string {
		$preamble = pack("CCC", 0xEF, 0xBB, 0xBF);
		$content = $utf8Text;
		switch ($encoding) {
			case ReliableTxtEncoding::UTF_16:
				$preamble = pack("CC", 0xFE, 0xFF);
				$content = self::convertUtf8ToUtf16($utf8Text);
				break;
			case ReliableTxtEncoding::UTF_16_REVERSE:
				$preamble = pack("CC", 0xFF, 0xFE);
				$content = self::convertUtf8ToUtf16Reverse($utf8Text);
				break;
			case ReliableTxtEncoding::UTF_32:
				$preamble = pack("CCCC", 0x00, 0x00, 0xFE, 0xFF);
				$content = self::convertUtf8ToUtf32($utf8Text);
				break;
		}
		return $preamble . $content;
	}
	
	private static function convertUtf8ToUtf16(string $utf8Text) : string {
		return mb_convert_encoding($utf8Text, "UTF-16BE", "UTF-8");
	}
		
	private static function convertUtf8ToUtf16Reverse(string $utf8Text) : string {
		return mb_convert_encoding($utf8Text, "UTF-16LE", "UTF-8");
	}
		
	private static function convertUtf8ToUtf32(string $utf8Text) : string {
		return mb_convert_encoding($utf8Text, "UTF-32BE", "UTF-8");
	}
}

?>