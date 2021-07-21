<?php

namespace Stenway\ReliableTxt;

abstract class ReliableTxtDecoder {
	static function getEncoding(string $bytes) : int {
		$length = strlen($bytes);
		if ($length >= 3 
				&& ord($bytes[0]) == 0xEF
				&& ord($bytes[1]) == 0xBB
				&& ord($bytes[2]) == 0xBF) {
			return ReliableTxtEncoding::UTF_8;
		} elseif ($length >= 2 
				&& ord($bytes[0]) == 0xFE
				&& ord($bytes[1]) == 0xFF) {
			return ReliableTxtEncoding::UTF_16;
		} elseif ($length >= 2
				&& ord($bytes[0]) == 0xFF
				&& ord($bytes[1]) == 0xFE) {
			return ReliableTxtEncoding::UTF_16_REVERSE;
		} elseif ($length >= 4
				&& ord($bytes[0]) == 0x00
				&& ord($bytes[1]) == 0x00
				&& ord($bytes[2]) == 0xFE
				&& ord($bytes[3]) == 0xFF) {
			return ReliableTxtEncoding::UTF_32;
		}
		throw new Exception("Document does not have a ReliableTXT preamble");
	}
	
	static function decode(string $bytes) : array {
		$detectedEncoding = self::getEncoding($bytes);
		
		$content = $bytes;
		switch ($detectedEncoding) {
			case ReliableTxtEncoding::UTF_16:
				$content = self::convertUtf16ToUtf8($bytes);
				break;
			case ReliableTxtEncoding::UTF_16_REVERSE:
				$content = self::convertUtf16ReverseToUtf8($bytes);
				break;
			case ReliableTxtEncoding::UTF_32:
				$content = self::convertUtf32ToUtf8($bytes);
				break;
		}
		$content = mb_substr($content, 1);
		return [$detectedEncoding, $content];
	}
	
	private static function convertUtf16ToUtf8(string $utf16Text) : string {
		return mb_convert_encoding($utf16Text, "UTF-8", "UTF-16BE");
	}
	
	private static function convertUtf16ReverseToUtf8(string $utf16ReverseText) : string {
		return mb_convert_encoding($utf16ReverseText, "UTF-8", "UTF-16LE");
	}
	
	private static function convertUtf32ToUtf8(string $utf32Text) : string {
		return mb_convert_encoding($utf32Text, "UTF-8", "UTF-32BE");
	}
}

?>