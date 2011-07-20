<?php 
/**
 * Pagination view for the frontend reports
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

/**
 * Frontend  pagination style
 * 
 * @preview  <<Prev 1 … 4 5 6 7 8 … 15 Next>>
 */
?>

	<ul class="pager">
	
		<?php if ($total_pages < 10): /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */ ?>

			<?php for ($i = 1; $i <= $total_pages; $i++): ?>
				<?php if ($i == $current_page): ?>
					<li><span><a href="#" class="active"><?php echo $i ?></a></span></li>
				<?php else: ?>
					<li><span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span></li>
				<?php endif ?>
			<?php endfor ?>

		<?php elseif ($current_page < 6): /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */ ?>

			<?php for ($i = 1; $i <= 6; $i++): ?>
				<?php if ($i == $current_page): ?>
					<li><span><a class="active" href="#"><?php echo $i ?></a></span></li>
				<?php else: ?>
					<li><span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span></li>
				<?php endif ?>
			<?php endfor; ?>

			<li>&hellip;</li>
			<li><span><a href="<?php echo str_replace('{page}', $total_pages - 1, $url) ?>"><?php echo $total_pages - 1 ?></a></span></li>
			<li><span><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></span></li>

		<?php elseif ($current_page < 50): ?>	
			<li><span><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></span></li>
			<li>&hellip;</li>
			<?php $num_subtract = ($current_page > 10)? 1:  2; ?>	
			<?php for ($i = $current_page - $num_subtract; $i <= $current_page + 2; $i++): ?>
				<?php if ($i == $current_page): ?>
					<li><span><a href="#" class="active"><?php echo $i ?></a></span></li>
				<?php else: ?>
					<li><span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span></li>
				<?php endif ?>
			<?php endfor; ?>
			
			<li>&hellip;</li>
			<li><span><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></span></li>
			
		<?php elseif ($current_page >= $total_pages - 3): /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */ ?>
			<li><span><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></span></li>
			<li>&hellip;</li>
			<?php $start_page = ($current_page == $total_pages)? 5 : 2; ?>	
			<?php for ($i = $current_page - $start_page; $i <= $total_pages; $i++): ?>
				<?php if ($i == $current_page): ?>
					<li><span><a href="#" class="active"><?php echo $i ?></a></span></li>
				<?php else: ?>
					<li><span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span></li>
				<?php endif ?>
			<?php endfor ?>
			
		<?php else: /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */ ?>

			<li><span><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></span></li>
			<li>&hellip;</li>

			<?php for ($i = $current_page - 2; $i <= $current_page + 1; $i++): ?>
				<?php if ($i == $current_page): ?>
					<li><span><a class="active" href="#"><?php echo $i ?></a></span></li>
				<?php else: ?>
					<li><span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span></li>
				<?php endif ?>
			<?php endfor ?>

			<li>&hellip;</li>
			<li><span><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></span></li>

		<?php endif ?>

	</ul>