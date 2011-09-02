<?php
/**
 * Report Importer Library
 *
 * Imports reports within CSV file referenced by filehandle.
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
class ReportsImporter {
	
	function __construct() 
	{
		$this->notices = array();
		$this->errors = array();		
		$this->totalrows = 0;
		$this->importedrows = 0;
		$this->incidents_added = array();
		$this->categories_added = array();
		$this->locations_added = array();
		$this->incident_categories_added = array();
	}
	
	/**
	 * Function to import CSV file referenced by the file handle
	 * @param string $filehandle
	 * @return bool 
	 */
	function import($filehandle) 
	{
		$csvtable = new Csvtable($filehandle);
		// Set the required columns of the CSV file
		$requiredcolumns = array('INCIDENT TITLE','INCIDENT DATE');
		foreach ($requiredcolumns as $requiredcolumn)
		{
			// If the CSV file is missing any required column, return an error
			if (!$csvtable->hasColumn($requiredcolumn))
			{
				$this->errors[] = 'CSV file is missing required column "'.$requiredcolumn.'"';
			}
		}
		
		if (count($this->errors))
		{
			return false;
		}
		
		// So we can assign category id to incidents, based on category title
		$this->category_ids = ORM::factory('category')->select_list('category_title','id'); 
		//Since we capitalize the category names from the CSV file, we should also capitlize the 
		//category titles here so we get case insensative behavior. For some reason users don't
		//always captilize the cateogry names as they enter them in
		$temp_cat = array();
		foreach($this->category_ids as $key=>$value)
		{
			$temp_cat[strtoupper($key)] = $value;
		}
		$this->category_ids = $temp_cat;
		
		// So we can check if incident already exists in database
		$this->incident_ids = ORM::factory('incident')->select_list('id','id'); 
		$this->time = date("Y-m-d H:i:s",time());
		$rows = $csvtable->getRows();
		$this->totalrows = count($rows);
		$this->rownumber = 0;
	 	
		// Loop through CSV rows
	 	foreach($rows as $row)
	 	{
			$this->rownumber++;
			if (isset($row['#']) AND isset($this->incident_ids[$row['#']]))
			{
				$this->notices[] = 'Incident with id #'.$row['#'].' already exists.';
			}
			else
			{
				if ($this->importreport($row))
				{
					$this->importedrows++;
				}
				else
				{
					$this->rollback();
					return false;
				}
			}
		} 
		return true;
	}
	
	/**
	 * Function to undo import of reports
	 */
	function rollback()
	{
		if (count($this->incidents_added)) ORM::factory('incident')->delete_all($this->incidents_added);
		if (count($this->categories_added)) ORM::factory('category')->delete_all($this->categories_added);
		if (count($this->locations_added)) ORM::factory('location')->delete_all($this->locations_added);
		if (count($this->incident_categories_added)) ORM::factory('location')->delete_all($this->incident_categories_added);
	}
	
	/**
	 * Function to import a report form a row in the CSV file
	 * @param array $row
	 * @return bool
	 */
	function importreport($row)
	{
		// If the date is not in proper date format
		if (!strtotime($row['INCIDENT DATE']))
		{
			$this->errors[] = 'Could not parse incident date "'.htmlspecialchars($row['INCIDENT DATE']).'" on line '
			.($this->rownumber+1);
		}
		// If a value of Yes or No is NOT set for approval status for the imported row
		if (isset($row["APPROVED"]) AND !in_array($row["APPROVED"],array('NO','YES')))
		{
			$this->errors[] = 'APPROVED must be either YES or NO on line '.($this->rownumber+1);
		}
		// If a value of Yes or No is NOT set for verified status for the imported row 
		if (isset($row["VERIFIED"]) AND !in_array($row["VERIFIED"],array('NO','YES'))) 
		{
			$this->errors[] = 'VERIFIED must be either YES or NO on line '.($this->rownumber+1);
		}
		if (count($this->errors)) 
		{
			return false;
		}
		
		// STEP 1: SAVE LOCATION
		if (isset($row['LOCATION']))
		{
			$location = new Location_Model();
			$location->location_name = isset($row['LOCATION']) ? $row['LOCATION'] : '';
			$location->latitude = isset($row['LATITUDE']) ? $row['LATITUDE'] : '';
			$location->longitude = isset($row['LONGITUDE']) ? $row['LONGITUDE'] : '';
			$location->location_date = $this->time;
			$location->save();
			$this->locations_added[] = $location->id;
		}
		
		// STEP 2: SAVE INCIDENT
		$incident = new Incident_Model();
		$incident->location_id = isset($row['LOCATION']) ? $location->id : 0;
		$incident->user_id = 0;
		$incident->incident_title = $row['INCIDENT TITLE'];
		$incident->incident_description = isset($row['DESCRIPTION']) ? $row['DESCRIPTION'] : '';
		$incident->incident_date = date("Y-m-d H:i:s",strtotime($row['INCIDENT DATE']));
		$incident->incident_dateadd = $this->time;
		$incident->incident_active = (isset($row['APPROVED']) AND $row['APPROVED'] == 'YES') ? 1 : 0;
		$incident->incident_verified = (isset($row['VERIFIED']) AND $row['VERIFIED'] == 'YES') ? 1 :0;
		$incident->save();
		$this->incidents_added[] = $incident->id;
		
		// STEP 3: SAVE CATEGORIES
		// If CATEGORIES column exists
		if (isset($row['CATEGORY']))
		{
			$categorynames = explode(',',trim($row['CATEGORY']));
			// Add categories to incident
			foreach ($categorynames as $categoryname)
			{
				// There seems to be an uppercase convention for categories... Don't know why
				$categoryname = strtoupper(trim($categoryname)); 
				// Empty categoryname not allowed
				if ($categoryname != '')
				{
					if (!isset($this->category_ids[$categoryname]))
					{
						$this->notices[] = 'There exists no category "'.htmlspecialchars($categoryname).'" in database yet.'
						.' Added to database.';
						$category = new Category_Model;
						$category->category_title = $categoryname;
						// We'll just use black for now. Maybe something random?
						$category->category_color = '000000'; 
						// because all current categories are of type '5'
						$category->category_type = 5; 
						$category->category_visible = 1;
						$category->category_description = $categoryname;
						$category->save();
						$this->categories_added[] = $category->id;
						// Now category_id is known: This time, and for the rest of the import.
						$this->category_ids[$categoryname] = $category->id; 
					}
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = $this->category_ids[$categoryname];
					$incident_category->save();
					$this->incident_categories_added[] = $incident_category->id;
				} 
			} 
		} 
		return true;
	}
}

?>
