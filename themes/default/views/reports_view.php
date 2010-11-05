<div id="main" style="width:310px;float:left;">

	<div id="mainmiddle" class="floatbox withright">
		<!-- start incident block -->
		<div class="reports">
			<div class="report-details">
				<div class="verified <?php
				if ($incident_verified == 1)
				{
					echo " verified_yes";
				}
				?>"><?php
					echo ($incident_verified == 1) ?
						"<span>Verified</span>" :
						"<span>Unverified</span>";
					?>
				</div>
				<h1><?php
				echo $incident_title;
				
				// If Admin is Logged In - Allow For Edit Link
				if ($logged_in)
				{
					echo " [&nbsp;<a href=\"".url::site()."admin/reports/edit/".$incident_id."\">Edit</a>&nbsp;]";
				}
				?></h1>
				<ul class="details">
					<li>
						<small><?php echo Kohana::lang('ui_main.location');?></small>
						<?php echo $incident_location; ?>
					</li>
					<li>
						<small><?php echo Kohana::lang('ui_main.date');?></small>
						<?php echo $incident_date; ?>
					</li>
					<li>
						<small><?php echo Kohana::lang('ui_main.time');?></small>
						<?php echo $incident_time; ?>
					</li>
					<li>
						<small><?php echo Kohana::lang('ui_main.category');?></small>
						<?php
							foreach($incident_category as $category) 
							{ 
								echo "<a href=\"".url::site()."reports/?c=".$category->category->id."\">" .
								$category->category->category_title . "</a>&nbsp;&nbsp;&nbsp;";
							}
						?>
					</li>
					<?php
					// Action::report_meta - Add Items to the Report Meta (Location/Date/Time etc.)
					Event::run('ushahidi_action.report_meta', $incident_id);
					?>
				</ul>
			</div>
			<div class="clearingfix"></div>
			<div class="location" style="margin-top:20px;">
				<div class="incident-notation clearingfix">
					<ul>
						<li><img align="absmiddle" alt="<?php echo Kohana::lang('ui_main.report');?>" src="<?php echo url::base(); ?>media/img/incident-pointer.jpg"/> <?php echo Kohana::lang('ui_main.report');?></li>
						<li><img align="absmiddle" alt="<?php echo Kohana::lang('ui_main.nearby_report');?>" src="<?php echo url::base(); ?>media/img/nearby-incident-pointer.jpg"/> <?php echo Kohana::lang('ui_main.nearby_report');?></li>
					</ul>
				</div>
				<div class="report-map">
					<div class="map-holder" id="map"></div>
				</div>
				
				<?php if( count($feeds) > 0 ) { ?>
				<div class="report-description" style="float:left;width:292px;margin:15px 0px;">
					<h3><?php echo Kohana::lang('ui_main.news_feeds');?></h3>
					<table cellpadding="0" cellspacing="0">
						<tr class="title">
							<th class="w-01"><?php echo Kohana::lang('ui_main.title');?></th>
							<th class="w-02"><?php echo Kohana::lang('ui_main.source');?></th>
							<th class="w-03"><?php echo Kohana::lang('ui_main.date');?></th>
						</tr>
						<?php
							foreach ($feeds as $feed)
							{
								$feed_id = $feed->id;
								$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
								$feed_link = $feed->item_link;
								$feed_date = date('M j Y', strtotime($feed->item_date));
								$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
						?>
						<tr>
							<td class="w-01">
								<a href="<?php echo $feed_link; ?>" target="_blank">
								<?php echo $feed_title ?></a>
							</td>
							<td class="w-02"><?php echo $feed_source; ?></td>
							<td class="w-03" nowrap><?php echo $feed_date; ?></td>
						</tr>
						<?php
						}
						?>
					</table>
				</div>
				<?php } ?>
				
			</div>
			
		</div>
	</div>
</div>

	<div class="report-description" style="float:right;width:520px;margin-top:35px;">

		<h3><?php echo Kohana::lang('ui_main.report_details');?></h3>
		<h5><?php echo Kohana::lang('ui_main.reports_description');?></h5>
		<div class="content">
			<?php echo $incident_description; ?>
			<div class="credibility">
				<?php echo Kohana::lang('ui_main.credibility');?>:
				<a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-up.jpg" alt="UP" title="UP" border="0" /></a>&nbsp;
				<a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-down.jpg" alt="DOWN" title="DOWN" border="0" /></a>&nbsp;
				<a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a>
				<a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a>
			</div>
		</div>
		
		<!-- start images -->
		<?php if( count($incident_photos) > 0 ) { ?>
			<h5><?php echo Kohana::lang('ui_main.images');?></h5>
			<div class="content">
					<?php
					foreach ($incident_photos as $photo)
					{
						$thumb = str_replace(".","_t.",$photo);
						$prefix = url::base().Kohana::config('upload.relative_directory');
						echo '<a class="photothumb" rel="lightbox-group1" href="'.$prefix.'/'.$photo.'"><img src="'.$prefix.'/'.$thumb.'"/></a> ';
					}
					?>
			</div>
		<?php } ?>
		<!-- end images -->
		
		<!-- start videos -->
		<?php if( count($incident_videos) > 0 ) { ?>
			<h5><?php echo Kohana::lang('ui_main.video');?></h5>
			<div class="content">
				<?php
					// embed the video codes
					foreach( $incident_videos as $incident_video) {
						$videos_embed->embed($incident_video,'');
					}
				?>
			</div>
		<?php } ?>
		<!-- end videos -->
		
		<?php
		// Action::report_extra - Add Items to the Report Extra block
		Event::run('ushahidi_action.report_extra', $incident_id);
		?>
		
		<?php
		// Filter::comments_form_block - The block that contains the comments form
		Event::run('ushahidi_filter.comment_form_block', $comments_form);
		echo $comments_form;
		?>
		
		<div class="orig-report">
			<div id="comments" class="discussion">
				
				<h5><?php echo Kohana::lang('ui_main.comments'); ?>&nbsp;&nbsp;&nbsp;(<a id="showcomment"><?php echo Kohana::lang('ui_main.add'); ?> <?php echo Kohana::lang('ui_main.comment'); ?></a>)</h5>
				
				<script type="text/javascript">
					
					$('#showcomment').click(function() {
						$('.comment-block').show('slow', function() {
						// Animation complete.
						});
					});

				</script>
				
				<?php
				// Filter::comments_block - The block that contains posted comments
				Event::run('ushahidi_filter.comment_block', $comments);
				echo $comments;
				?>
			</div>
		</div>
		
	</div>

	<div class="report-description" style="float:right;width:520px;">
		<h3><?php echo Kohana::lang('ui_main.additional_reports');?></h3>
		<table cellpadding="0" cellspacing="0">
			<tr class="title">
				<th class="w-01"><?php echo Kohana::lang('ui_main.title');?></th>
				<th class="w-02"><?php echo Kohana::lang('ui_main.location');?></th>
				<th class="w-03"><?php echo Kohana::lang('ui_main.date');?></th>
			</tr>
			<?php foreach($incident_neighbors as $neighbor) { ?>
			<tr>
				<td class="w-01"><a href="<?php echo url::site(); ?>reports/view/<?php echo $neighbor->id; ?>"><?php echo $neighbor->incident_title; ?></a></td>
				<td class="w-02"><?php echo $neighbor->location->location_name; ?></td>
				<td class="w-03" nowrap><?php echo date('M j Y', strtotime($neighbor->incident_date)); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<div style="clear:both;"></div>
	
</div>