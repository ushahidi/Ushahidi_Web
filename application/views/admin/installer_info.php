<?php
/**
 * Installer Info View File.
 *
 * Used to render the HTML for installer information - deleting installer
 * folder afer installation
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Linda Kamau <codediva@codediva.co.ke>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php 
if (file_exists(DOCROOT.DIRECTORY_SEPARATOR.'installer'))
{ ?>
	<?php
	$warning = "<li>" .Kohana::lang('ui_admin.installer_info'). "</li>";
	?>	
	<div id="security-info" class="update-info">
	<h4>Installer Files Warning:</h4>
	<ul>
		<?php
			echo $warning;
		?>
	</ul>
	</div>
	<?php
} ?>
