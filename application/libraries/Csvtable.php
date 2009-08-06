<?php
/**
 * CSV Table
 * Simple class to load arbitrary CSV files as an array of associative arrays.
 * Uses first line of the file as column names.
 * @package    CSVTable
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Csvtable_Core {
	var $columnnames = array();
	
	function __construct($filehandle) {
		$this->filehandle = $filehandle;
		if(($fields = fgetcsv($filehandle, 1000)) !== FALSE) { // 1000 chars is max line length
			$colnum = 0;
			foreach($fields as $field) {
				$this->colnames[$field] = $colnum;
				$colnum++;
			} // fields
		}		
	}
	
	function hasColumn($name) {
		return isset($this->colnames[$name]);
	}
	
	function getRows() {
		$rows = array();
		$numcols = count($this->columnnames);
		while (($fields = fgetcsv($this->filehandle, 1000, ",")) !== FALSE) {
			foreach ($this->colnames as $colname => $colnum) {
				$row[$colname] = isset($fields[$colnum]) ? $fields[$colnum] : '';
			}
			$rows[] = $row;
		}
		return $rows;
	}	
}
?>