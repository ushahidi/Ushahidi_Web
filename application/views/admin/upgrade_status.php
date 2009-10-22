<?php 
/**
 * Upgrade status view page.
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
	<h2><?php echo $title; ?></h2>
	
	<?php if (isset($logs) && count($logs) ) { ?>
	<h3>Upgrading</h3>
	<ul>
		<?php foreach( $logs as $log ) { ?>
			<li><?php echo $log ?></li>
		<?php }?>
	</ul> 
	
	<?php }?>
	
	<?php if( isset($errors ) && count($errors)){  ?>  
	<h3>Upgrade failed at some point</h3>	
		<ul>
	<?php foreach($errors as $error)  { ?>
	<li><?php echo $error ?></li>

	<?php } }?>
	</ul>
	</div>
</div>