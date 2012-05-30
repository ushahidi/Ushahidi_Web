<?php 
/**
 * Comments view page.
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
				<h2>
					<?php admin::reports_subtabs("comments"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='p' && $status !='s') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=p" <?php if ($status == 'p') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.pending');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.approved');?></a></li>
						<li><a href="?status=s" <?php if ($status == 's') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.spam');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="commentAction('a','APPROVE', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.approve'));?></a></li>
							<li><a href="#" onclick="commentAction('u','UNAPPROVE', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.disapprove'));?></a></li>
							<li><a href="#" onclick="commentAction('s','MARK AS SPAM', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.spam'));?></a></li>
							<li><a href="#" onclick="commentAction('n','MARK AS NOT SPAM', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.not_spam'));?></a></li>
							<li><a href="#" onclick="commentAction('d','DELETE', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<?php 
							if ($status == 's')
							{
								?>
								<li><a href="#" onclick="commentAction('x','DELETE ALL SPAM', '000');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete_spam'));?></a></li>
								<?php
							}
							?>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error)
				{
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved)
				{
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_admin.comments'); ?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'commentMain', 'name' => 'commentMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="comment_id[]" id="comment_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallcomments" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'comment_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.comment_details');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
								</tr>
							</thead>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								if ($total_items == 0)
								{
								?>
									<tr>
										<td colspan="4" class="col">
											<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
										</td>
									</tr>
								<?php
								}
								foreach ($comments as $comment)
								{
									$comment_id = $comment->id;
									$comment_author = $comment->comment_author;
									$comment_description = $comment->comment_description;
									$comment_email = $comment->comment_email;
									$comment_ip = $comment->comment_ip;
									$comment_active = $comment->comment_active;
									$comment_spam = $comment->comment_spam;
									$comment_date = date('Y-m-d H:i', strtotime($comment->comment_date));

									$incident_id = $comment->incident->id;
									$incident_title = $comment->incident->incident_title;
									?>
									<tr>
										<td class="col-1"><input name="comment_id[]" id="comment" value="<?php echo $comment_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4><a href="<?php echo url::base() . 'reports/view/' . $incident_id; ?>"><?php echo html::specialchars($comment_author); ?></a></h4>
												<?php
												if ($incident_title != "")
												{
													?><div class="comment_incident"><?php echo Kohana::lang('ui_main.in_response_to');?>: <strong><a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>"><?php echo strip_tags($incident_title); ?></a></strong></div><?php
												}
												?>
												<p><?php echo html::specialchars($comment_description); ?></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.email');?>: <strong><?php echo $comment_email; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.ip_address');?>: <strong><?php echo $comment_ip; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $comment_date; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><?php
												if ($comment_active)
												{
													?><a href="#" class="status_yes" onclick="commentAction('u','UNAPPROVE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.approved');?></a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('a','APPROVE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.approve');?></a><?php
												}
												?></li>
												<li><?php
												if ($comment_spam)
												{
													?><a href="#" class="status_yes" onclick="commentAction('n','MARK AS NOT SPAM', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.not_spam');?></a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('s','MARK AS SPAM', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.spam');?></a><?php
												}
												?></li>
												<li><a href="#" class="del" onclick="commentAction('d','DELETE', '<?php echo $comment_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
											</ul>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>
