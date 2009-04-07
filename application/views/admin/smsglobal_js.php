/**
 * Sms global js file.
 * 
 * Handles javascript stuff related to the sms global function.
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
		// Retrieve Clickatell Balance (AJAX)
		function clickatellBalance()
		{
			$('#balance_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.get("<?php echo url::base() . 'admin/settings/smsbalance/' ?>", function(data){
				alert("RESPONSE: " + data);
				$('#balance_loading').html('');
			});
		}