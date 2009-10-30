<?php
/**
 * Parse a time/date generated with strftime().
 *
 * This function is the same as the original one defined by PHP (Linux/Unix only),
 *  but now you can use it on Windows too.
 *  Limitation : Only this format can be parsed %S, %M, %H, %d, %m, %Y
 *
 * @author Lionel SAURON
 * @version 1.0
 * @public
 *
 * @param $sDate(string)    The string to parse (e.g. returned from strftime()).
 * @param $sFormat(string)  The format used in date  (e.g. the same as used in strftime()).
 * @return (array)          Returns an array with the <code>$sDate</code> parsed, or <code>false</code> on error.
 */

if(function_exists("strptime") == false){
	function strptime($sDate, $sFormat){
		$aResult = array(
			'tm_sec'   => 0,
			'tm_min'   => 0,
			'tm_hour'  => 0,
			'tm_mday'  => 1,
			'tm_mon'   => 0,
			'tm_year'  => 0,
			'tm_wday'  => 0,
			'tm_yday'  => 0,
			'unparsed' => $sDate,
		);
		
		while($sFormat != ""){
			// ===== Search a %x element, Check the static string before the %x =====
			$nIdxFound = strpos($sFormat, '%');
			if($nIdxFound === false){
				// There is no more format. Check the last static string.
				$aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
				break;
			}
		}

		// ===== Create the other value of the result array =====
		$nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'],
								$aResult['tm_mon'] + 1, $aResult['tm_mday'], $aResult['tm_year'] + 1900);
		
		// Before PHP 5.1 return -1 when error
		if(($nParsedDateTimestamp === false)
		||($nParsedDateTimestamp === -1)) return false;
		
		$aResult['tm_wday'] = (int) strftime("%w", $nParsedDateTimestamp); // Days since Sunday (0-6)
		$aResult['tm_yday'] = (strftime("%j", $nParsedDateTimestamp) - 1); // Days since January 1 (0-365)
		
		return $aResult;
	} // END of function
   
} // END if(function_exists("strptime") == false)
?>