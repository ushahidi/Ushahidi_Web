<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Ushahidi View class
 * Adds extra hooks to views
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class View extends View_Core
{
	// Save name for use in hook
	protected $name = FALSE;
	
	public function __construct($name = NULL, $data = NULL, $type = NULL)
	{
		$this->name = $name;
		
		parent::__construct($name, $data, $type);
	}
	
	/**
	 * Renders a view.
	 * 
	 * Add an additional filter to modify view data
	 *
	 * @param   boolean   set to TRUE to echo the output instead of returning it
	 * @param   callback  special renderer to pass the output through
	 * @return  string    if print is FALSE
	 * @return  void      if print is TRUE
	 */
	public function render($print = FALSE, $renderer = FALSE)
	{
		// Run view_pre_render filter to allow plugins/themes to add extra data to a view
		Event::run('ushahidi_filter.view_pre_render', $this->kohana_local_data);
		// View specific hook pre render hook ie. ushahidi_filter.view_pre_render.reports_main
		Event::run('ushahidi_filter.view_pre_render.'.str_replace('/','_',$this->name), $this->kohana_local_data);
		
		return parent::render($print, $renderer);
	}
}
