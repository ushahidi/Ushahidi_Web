<?php
// This function lifted from php.net: http://www.php.net/manual/en/function.htmlentities.php#96648

// converts a UTF8-string into HTML entities
//  - $utf8:        the UTF8-string to convert
//  - $encodeTags:  booloean. TRUE will convert "<" to "&lt;"
//  - return:       returns the converted HTML-string
class utf8tohtml {
	public function convert($utf8, $encodeTags) {
		$result = '';
		for ($i = 0; $i < strlen($utf8); $i++) {
			$char = $utf8[$i];
			$ascii = ord($char);
			if ($ascii < 128) {
				// one-byte character
				$result .= ($encodeTags) ? htmlentities($char) : $char;
			} else if ($ascii < 192) {
				// non-utf8 character or not a start byte
			} else if ($ascii < 224) {
				// two-byte character
				$result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
				$i++;
			} else if ($ascii < 240) {
				// three-byte character
				$ascii1 = ord($utf8[$i+1]);
				$ascii2 = ord($utf8[$i+2]);
				$unicode = (15 & $ascii) * 4096 +
							(63 & $ascii1) * 64 +
							(63 & $ascii2);
				$result .= "&#$unicode;";
				$i += 2;
			} else if ($ascii < 248) {
				// four-byte character
				$ascii1 = ord($utf8[$i+1]);
				$ascii2 = ord($utf8[$i+2]);
				$ascii3 = ord($utf8[$i+3]);
				$unicode = (15 & $ascii) * 262144 +
							(63 & $ascii1) * 4096 +
							(63 & $ascii2) * 64 +
							(63 & $ascii3);
				$result .= "&#$unicode;";
				$i += 3;
			}
		}
		return $result;
	}
}
?>