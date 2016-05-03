<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Analysis/Incident Model
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Analysis_Incident Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Analysis_Incident_Model extends ORM
{	
	protected $belongs_to = array('analysis');
	
	// Database table name
	protected $table_name = 'analysis_incident';
}
