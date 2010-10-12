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

class AdminEmail
{
    private $data;
    private $items;
    private $table_prefix;
    private $api_actions;
    private $response_type;

    public function __construct()
    {
    }

    /**
     * List first 15 email messages
     *
     * @return array
     */
    public function _list_all_email_msgs()
    {
    }

    /**
     * Edit existing report
     *
     * @param int report_id - the id of the email message to be edited
     *
     * @return array
     */
    public function _edit_report($report_id)
    {
    }

    /**
     * Delete existing SMS message
     *
     * @param int email_msg_id - the id of the email message to be deleted.
     */
    public function _del_email_msg($email_msg_id)
    {
    }

}

