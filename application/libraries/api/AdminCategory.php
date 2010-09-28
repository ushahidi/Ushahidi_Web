<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 24 - Henry Addo 2010-09-27
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

require_once('ApiActions.php');

class AdminCategory
{
    
    private $data;
    private $items;
    private $table_prefix;
    private $api_actions;
    private $response_type;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->response_type = '';
    }

    /**
     * Add new category 
     */
    public function _add_category()
    {
    }
    
    /**
     * Edit existing category
     *
     * @param int category_id - the category id to be edited
     * 
     * @return array
     */
    public function _edit_category($category_id)
    {
    }

    /**
     * Delete existing category
     *
     * @param int category_id - the category id to be deleted.
     *
     * @return array
     */
    public function _del_category($category_id)
    {
    }

    
}
