<?php
/**
 * CSV Table
 *
 * Simple class to load arbitrary CSV files as an array of associative arrays.
 * Uses first line of the file as column names.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 *
 */
class Csvtable_Core {
	/**
	 * Column names
	 * @var array
	 */
	private $columnnames = array();
	
	function __construct($filehandle)
	{
		$this->filehandle = $filehandle;
		// 1000 chars is max line length
		if(($fields = fgetcsv($filehandle, 1000)) !== FALSE) 
		{ 
			$colnum = 0;
			foreach($fields as $field)
			{
				$this->colnames[$field] = $colnum;
				$colnum++;
			}
		}		
	}
	
	/**
	 * Function to check if the CSV File has a column in the name given
	 * @param string $name Name of column to be checked
	 * @return bool
	 */
	function hasColumn($name)
	{
		return isset($this->colnames[$name]);
	}
	
	/**
	 * Function to get rows in the CSV files
	 * @return array
	 */
	function getRows()
	{
		$rows = array();
		$numcols = count($this->columnnames);
		while (($fields = fgetcsv($this->filehandle, 4000, ",")) !== FALSE)
		{
			foreach ($this->colnames as $colname => $colnum)
			{
				$row[$colname] = isset($fields[$colnum]) ? $fields[$colnum] : '';
			}
			$rows[] = $row;
		}
		return $rows;
	}	
}
?>