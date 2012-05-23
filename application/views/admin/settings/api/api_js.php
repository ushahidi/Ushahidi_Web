<?php
/**
 * API settings js file.
 *
 * Handles javascript stuff related  to api log function
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
?>

<?php require SYSPATH.'../application/views/admin/utils_js.php' ?>

    // Ajax submission
    function apiSettingsAction(action, confirmAction)
    {
        // Display confirm dialog
        var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?');

        if (answer) {
            $("#action").attr("value", action);

            if (action == 's') // Save action therefore submit form
            {
                $("#apiSettingsMain").submit();
            }
            else // Cancel form submission
            {
                $("#apiSettingsMain").cancel();
            }
        }
        else
        {
            return false;
        }
    }