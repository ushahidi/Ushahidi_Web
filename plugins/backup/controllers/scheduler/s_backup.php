<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Backup Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Backup Scheduler Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class S_Backup_Controller extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		// Do the backup.
		$backup = new backup;
		$backup->go();
	}

	
}
