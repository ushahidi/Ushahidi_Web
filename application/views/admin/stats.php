<?php 
/**
 * Feedback view page.
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
<div class="bg">
	<h2>Statistics <span>small stat line.</span></h2>
	
	<div>
		<?php
			if($stat_id == 0){ // No stat account created
		?>
				<a href="?create_account=1">Create stat account</a>
		<?php
			}else{
		?>
				<div>
					<strong>Congratulations, you are currently set up to collect statistics.</strong>
				</div>
				<div>
					<img src="http://blog.jquery.com/wp-content/uploads/2007/12/flot-example.png" />
				</div>
		<?php
			}
		?>
	</div>
	
	<?php print form::close(); ?>
	
</div>

