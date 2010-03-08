<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Contact Us Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Contact Us Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class MHI_Controller extends Template_Controller
{
	 
	// MHI template
    public $template = 'layout';
	
	function __construct()
    {
        parent::__construct();

        // Load Header & Footer
        $this->template->header  = new View('mhi_header');
        $this->template->footer  = new View('mhi_footer');
    }

    public function index()
    {
    	$this->template->header->this_page = 'mhi';
        $this->template->content = new View('mhi');
		
    }
    
    public function signup()
    {
    	$this->template->header->this_page = 'mhi';
        $this->template->content = new View('mhi_signup');
    }
}