<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Pages controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Pages Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Page_Controller extends Main_Controller {
	
	function __construct()
    {
        parent::__construct();	
    }

	public function index($page_id = 1) 
	{
		$this->template->header->this_page = "page_".$page_id;
        $this->template->content = new View('page');
		
		if (!$page_id)
		{
			url::redirect('main');
		}
		
		$page = ORM::factory('page',$page_id)->find();
		if ($page->loaded)
		{
			$this->template->content->page_title = $page->page_title;
			$this->template->content->page_description = $page->page_description;
		}
		else
		{
			url::redirect('main');
		}
	}

}