<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class habours all the API objects and make them available to 
 * the API controller.
 *
 * @version 22 - David Kobia 2010-08-30
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

include Kohana::find_file('libraries/api','GetCategories');
include Kohana::find_file('libraries/api', 'GetApiKey');
include Kohana::find_file('libraries/api', 'GetCountries');
include Kohana::find_file('libraries/api', 'GetLocations');

class ApiObjects_Core
{
    public $categories;
    public $api_key;
    public $api_actions;

    public function __construct()
    {
        // create object of the get methods.
        $this->api_actions = new ApiActions;
        $this->categories = new GetCategories;
        $this->api_key = new GetApiKey;
        $this->countries = new GetCountries;
        $this->locations = new GetLocations;
    }

}
