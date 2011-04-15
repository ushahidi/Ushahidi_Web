<?php 
/**
 * pagination view.
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

/**
 * Ushahidi pagination style
 * 
 * @preview  Pages: 1 … 4 5 6 7 8 … 15
 */
?>

<ul class="pager">

	<li class="first"><?php echo $total_pages . " " . Kohana::lang('ui_main.pages') ?></li>

	<?php if ($current_page > 10): ?>
		<li><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></li>
		<?php if ($current_page != 11) echo '&hellip;' ?>
	<?php endif ?>


	<?php for ($i = $current_page - 9, $stop = $current_page + 10; $i < $stop; ++$i): ?>

		<?php if ($i < 1 OR $i > $total_pages) continue ?>

		<?php if ($current_page == $i): ?>
			<li><a href="#" class="active"><?php echo $i ?></a></li>
		<?php else: ?>
			<li><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></li>
		<?php endif ?>

	<?php endfor ?>


	<?php if ($current_page <= $total_pages - 10): ?>
		<?php if ($current_page != $total_pages - 10) echo '&hellip;' ?>
		<li><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></li>
	<?php endif ?>

</ul>