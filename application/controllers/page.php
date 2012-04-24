<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Pages controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
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

		if ( ! $page_id)
		{
			url::redirect('main');
		}

		$page = ORM::factory('page',$page_id)->find($page_id);
		if ($page->loaded)
		{

			$page_title = $page->page_title;
			$page_description = $page->page_description;
			// Filter::page_title - Modify Page Title
			Event::run('ushahidi_filter.page_title', $page_title);
			// Filter::page_description - Modify Page Description
			Event::run('ushahidi_filter.page_description', $page_description);

			$this->template->content->page_title = $page_title;
			$this->template->content->page_description = $page_description;
			$this->template->content->page_id = $page->id;
		}
		else
		{
			url::redirect('main');
		}

		$this->template->header->page_title .= $page_title.Kohana::config('settings.title_delimiter');

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

}
