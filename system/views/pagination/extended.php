<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Extended pagination style
 * 
 * @preview  « Previous | Page 2 of 11 | Showing items 6-10 of 52 | Next »
 */
?>

<p class="pagination">

	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?></a>
	<?php else: ?>
		&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?>
	<?php endif ?>

	| <?php echo Kohana::lang('pagination.page') ?> <?php echo $current_page ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_pages ?>

	| <?php echo Kohana::lang('pagination.items') ?> <?php echo $current_first_item ?>&ndash;<?php echo $current_last_item ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_items ?>

	| <?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>"><?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</a>
	<?php else: ?>
		<?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;
	<?php endif ?>

</p>