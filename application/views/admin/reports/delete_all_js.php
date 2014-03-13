/**
 * Delete all js file.
 *
 * Handles javascript stuff related to the delete all function.
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

		$(document).ready(function() {
			$(document.getElementById("reportForm")).submit(function () {
				return window.confirm("<?php echo addslashes(Kohana::lang('ui_admin.delete_all_confirm')); ?>");
			});
		});