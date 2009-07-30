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
				<h2><?php echo $title; ?> <span>(<?php echo $total_items; ?>)</span></h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='p' && $status !='s') echo "class=\"active\""; ?>>Show All</a></li>
						<li><a href="?status=p" <?php if ($status == 'p') echo "class=\"active\""; ?>>Pending</a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>>Approved</a></li>
						<li><a href="?status=s" <?php if ($status == 's') echo "class=\"active\""; ?>>Spam</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="commentAction('a','APPROVE', '');">APPROVE</a></li>
							<li><a href="#" onclick="commentAction('u','UNAPPROVE', '');">UNAPPROVE</a></li>
							<li><a href="#" onclick="commentAction('s','MARK AS SPAM', '');">SPAM</a></li>
							<li><a href="#" onclick="commentAction('n','MARK AS NOT SPAM', '');">NOT SPAM</a></li>
							<li><a href="#" onclick="commentAction('d','DELETE', '');">DELETE</a></li>
							<?php 
							if ($status == 's')
							{
								?>
								<li><a href="#" onclick="commentAction('x','DELETE ALL SPAM', '000');">DELETE ALL SPAM</a></li>
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
						<h3>Error!</h3>
						<ul>Please verify that you have checked an item</ul>
					</div>
				<?php
				}

				if ($form_saved)
				{
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3>Comments <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
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
									<th class="col-2">Comment Details</th>
									<th class="col-3">Date</th>
									<th class="col-4">Actions</th>
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
											<h3>No Results To Display!</h3>
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
									$comment_rating = $comment->comment_rating;
									$comment_date = date('Y-m-d', strtotime($comment->comment_date));
									
									$incident_id = $comment->incident->id;
									$incident_title = $comment->incident->incident_title;
									?>
									<tr>
										<td class="col-1"><input name="comment_id[]" id="comment" value="<?php echo $comment_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4><?php echo $comment_author; ?></h4>
												<?php
												if ($incident_title != "")
												{
													?><div class="comment_incident">In response to: <strong><a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>"><?php echo $incident_title; ?></a></strong></div><?php
												}
												?>
												<p><?php echo $comment_description; ?></p>
											</div>
											<ul class="info">
												<li class="none-separator">Email: <strong><?php echo $comment_email; ?></strong></li>
												<li>IP Address: <strong><?php echo $comment_ip; ?></strong></li>
												<li>Comment Rating: <strong><?php echo $comment_rating; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $comment_date; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><?php
												if ($comment_active)
												{
													?><a href="#" class="status_yes" onclick="commentAction('u','UNAPPROVE', '<?php echo $comment_id; ?>');">Approved</a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('a','APPROVE', '<?php echo $comment_id; ?>');">Approve</a><?php
												}
												?></li>
												<li><?php
												if ($comment_spam)
												{
													?><a href="#" class="status_yes" onclick="commentAction('n','MARK AS NOT SPAM', '<?php echo $comment_id; ?>');">Spam</a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="commentAction('s','MARK AS SPAM', '<?php echo $comment_id; ?>');">Spam</a><?php
												}
												?></li>
												<li><a href="#" class="del" onclick="commentAction('d','DELETE', '<?php echo $comment_id; ?>');">Delete</a></li>
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
