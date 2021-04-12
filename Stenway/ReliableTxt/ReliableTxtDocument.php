<?php

namespace Stenway\ReliableTxt;

use \Exception as Exception;
use \IntlChar as IntlChar;

class ReliableTxtDocument {
	
	private string $text;
	private int $encoding;
	
	function __construct(string $utf8Text = "", int $encoding = ReliableTxtEncoding::UTF_8) {
		$this->setText($utf8Text);
		$this->setEncoding($encoding);
	}

	function __toString() : string {
		return $this->text;
	}
	
	function getText() : string {
		return $this->text;
	}
	
	function setText(string $utf8Text) {
		$this->text = $utf8Text;
	}
	
	function getEncoding() : int {
		return $this->encoding;
	}
	
	function setEncoding(int $encoding) {
		$this->encoding = $encoding;
	}
	
	function getLines() : array {
		return ReliableTxtDocument::split($this->text);
	}
	
	function setLines(string ...$utf8Lines) {
		$this->text = ReliableTxtDocument::split($utf8Lines);
	}
	
	function getCodePoints() : array {
		return array_map("IntlChar::ord", mb_str_split($this->text));
	}
	
	function setCodePoints(array $codePoints) {
		$this->text = implode(array_map("IntlChar::chr", $codePoints));
	}
	
	public function save(string $filePath) {
		$file = fopen($filePath, "w");
		$bytes = ReliableTxtEncoder::encode($this->text, $this->encoding);
		fwrite($file, $bytes);
		fclose($file);
	}
	
	static function load(string $filePath) : ReliableTxtDocument {
		$fileSize = filesize($filePath);
		$file = fopen($filePath, "rb");
		$bytes = fread($file, $fileSize);
		fclose($file);
		$decoderResult = ReliableTxtDecoder::decode($bytes);
		return new ReliableTxtDocument($decoderResult[1], $decoderResult[0]);
	}
}

?>