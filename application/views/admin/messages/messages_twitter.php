<?php 
/**
 * Twitter view page.
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
				<h2><?php echo $title; ?> <a href="<?php print url::site() ?>admin/messages">SMS</a> <a href="<?php print url::site() ?>admin/messages/twitter"><?php echo Kohana::lang('ui_main.twitter');?></a> <a href="<?php print url::site() ?>admin/messages/laconica"><?php echo Kohana::lang('ui_main.laconica');?></a> </h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.inbox');?></a></li>
						<li><a href="?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.outbox');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><!-- <a href="#" onClick="submitIds()">DELETE</a> --> <a href="#"><?php echo strtoupper(Kohana::lang('ui_main.delete_disabled'));?></a></li>
						</ul>
					</div>
				</div>
				<?php 
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php  
					print form::open(NULL, array('id' => 'messagesMain', 'name' => 'messagesMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'message_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.message_details');?></th>
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
								
								foreach ($tweets as $tweet)
								{
									$tweet_id = $tweet->id;
									$tweet_from = $tweet->tweet_from;
									$tweet_hashtag = $tweet->tweet_hashtag;
									$incident_id = $tweet->incident_id;
									$tweet_link = $tweet->tweet_link;
									$tweet_description = $tweet->tweet;
									$tweet_date = date('Y-m-d', strtotime($tweet->tweet_date));
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" id="message_id" value="<?php echo $tweet_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $tweet_description; ?></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.from');?>: <strong><a href="<?php echo $tweet_link; ?>" target="_blank"><?php echo $tweet_from; ?></a></strong>
												<?php
												if($tweet_hashtag == ''){ //if this was a direct report
													echo "<li class=\"none-separator\"><strong>". strtoupper(Kohana::lang('ui_main.direct_report'))."</strong>";
												}else{ //if this was found using a hashtag search
													echo "<li class=\"none-separator\">". Kohana::lang('ui_main.hashtag').": <strong>#".$tweet_hashtag."</strong>";
												}
												?>
											</ul>
										</td>
										<td class="col-3"><?php echo $tweet_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0) {
													echo "<li class=\"none-separator\"><a href=\"". url::site() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>".Kohana::lang('ui_main.view_report')."</strong></a></li>";
												}
												else
												{
													echo "<li class=\"none-separator\"><a href=\"". url::site() . 'admin/reports/edit?tid=' . $tweet_id ."\">".Kohana::lang('ui_main.create_report')."?</a></li>";
												}
												?>
												<li>
                                                <!-- <a href="<?php echo url::site().'admin/messages/delete/'.$tweet_id ?>" onclick="return confirm("<?php echo Kohana::lang('ui_main.action_confirm');?>")" class="del"><?php echo Kohana::lang('ui_main.delete');?></a> --></li>
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
