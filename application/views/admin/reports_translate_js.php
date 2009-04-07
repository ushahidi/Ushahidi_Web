/**
 * Reports translate js file.
 *
 * Handles javascript stuff related to reports translate function.
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
		/* Dynamic categories */
		$(document).ready(function() {			
			// Action on Save Only
			$("#save_only").click(function () {
				$("#save").attr("value", "1");
			});
			
			// Action on Cancel
			$("#cancel").click(function () {
				window.location.href='<?php echo url::base() . 'admin/reports/' ?>';
				return false;
			});
		});