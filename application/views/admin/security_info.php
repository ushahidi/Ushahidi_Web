<?php
/**
 * Security Info View File.
 *
 * Used to render the HTML for security misconfigurations
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Robbie Mackay <rm@robbiemackay.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php 
if ( Kohana::config('config.enable_security_info') == TRUE)
{ ?>
	<?php
	$warnings = array();
	if (Kohana::config('encryption.default.key') == "USHAHIDI-INSECURE" || Kohana::config('encryption.default.key') == "K0H@NA+PHP_7hE-SW!FtFraM3w0R|<")
	{ 
		$warnings[] = "<li>". Kohana::lang('ui_admin.security_info_encryption_key'). "</li>";
	}
	if (Kohana::config('config.site_protocol') == "http")
	{
		$warnings[] = "<li>". Kohana::lang('ui_admin.security_info_https'). "</li>";
	}
	if (file_exists(DOCROOT.DIRECTORY_SEPARATOR.'installer'))
	{
		$warnings[] = "<li>" .Kohana::lang('ui_admin.installer_info'). "</li>";
	}
	
	if (count($warnings) > 0) 
	{ ?>
	<div id="security-info" class="update-info">
	<h4>Security Warning:</h4>
	<ul>
		<?php
			echo implode("\n",$warnings);
		?>
	</ul>
	<?php echo Kohana::lang('ui_admin.security_info_instructions'); ?> <a href="http://wiki.ushahidi.com/display/WIKI/Securing+your+Ushahidi+deployment">Securing your Ushahidi deployment</a>
	</div>
	<?php
	}
} ?>