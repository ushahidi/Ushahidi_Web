<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Unit test for the https_check hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   HTTPS Check Hook Unit Test
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Hook_Https_Check_Test extends PHPUnit_Framework_TestCase {
    
    /**
     * Tests the protocol in $config['site_protocol'] against the URL
     */
    public function testSiteProtocol()
    {
        // Build the regular expression for site protocol
        $site_protocol = '/'.Kohana::config('core.site_protocol').':\/\//';
        
        // Check if the url base contains the site protocol
        $this->assertRegExp($site_protocol, url::base());
    }
}
?>