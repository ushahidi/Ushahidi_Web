<div id="main">

	<div style="float:left;width:500px">
	
		<div class="verified <?php
		if ($incident_verified == 1)
		{
			echo " verified_yes";
		}
		?>">
			<span><?php echo ($incident_verified == 1) ? Kohana::lang('ui_main.verified') : Kohana::lang('ui_main.unverified'); ?></span>
		</div>
	
		<h1 class="report-title"><?php
			echo $incident_title;
			
			// If Admin is Logged In - Allow For Edit Link
			if ($logged_in)
			{
				echo " [&nbsp;<a href=\"".url::site()."admin/reports/edit/".$incident_id."\">".Kohana::lang('ui_main.edit')."</a>&nbsp;]";
			}
		?></h1>
	
		<div class="report-when-where">
			<small><?php echo $incident_time.' '.$incident_date; ?> | <?php echo $incident_location; ?></small>
		</div>
	
		<div class="report-category-list">
			<small>
			<?php
				foreach($incident_category as $category) 
				{ 
					echo "<a href=\"".url::site()."reports/?c=".$category->category->id."\" class=\"r-3\" style=\"border-color:#".$category->category->category_color."\">".$category->category->category_title."</a>&nbsp;&nbsp;&nbsp;";
				}
			?>
			</small>
			<?php
			// Action::report_meta - Add Items to the Report Meta (Location/Date/Time etc.)
			Event::run('ushahidi_action.report_meta', $incident_id);
			?>
		</div>
		
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('ui_main.reports_description');?></h5>
			<?php echo $incident_description; ?>
			<div class="credibility">
				<?php echo Kohana::lang('ui_main.credibility');?>:
				<a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-up.jpg" alt="UP" title="UP" border="0" /></a>&nbsp;
				<a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-down.jpg" alt="DOWN" title="DOWN" border="0" /></a>&nbsp;
				<a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a>
				<a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a>
			</div>
		</div>
		
		<?php
			// Filter::comments_block - The block that contains posted comments
			Event::run('ushahidi_filter.comment_block', $comments);
			echo $comments;
		?>
		
		<?php
			// Filter::comments_form_block - The block that contains the comments form
			Event::run('ushahidi_filter.comment_form_block', $comments_form);
			echo $comments_form;
		?>
	
	</div>
	
	<div style="float:right;width:390px;">

		<div class="report-media-box-tabs">
			<ul>
				<li id="mapli" class="report-tab-selected"><a id="showmap"><?php echo Kohana::lang('ui_main.map');?></a></li>
				<?php if( count($incident_photos) > 0 ) { ?>
					<li id="imagesli"><a id="showimages"><?php echo Kohana::lang('ui_main.images');?></a></li>
				<?php } ?>
				<?php if( count($incident_videos) > 0 ) { ?>
					<li id="videoli"><a id="showvideo"><?php echo Kohana::lang('ui_main.video');?></a></li>
				<?php } ?>
			</ul>
		</div>
		
		<div class="report-media-box-content">
			
			<div class="report-map">
				<div class="map-holder" id="map"></div>
				<a id="showtallermap" style="float:right;margin-top:5px;font-size:12px;">&darr;&nbsp;&darr;&nbsp;&darr;&nbsp;&darr;</a>
				<a id="showshortermap" style="float:right;margin-top:5px;font-size:12px;display:none;">&uarr;&nbsp;&uarr;&nbsp;&uarr;&nbsp;&uarr;</a>
				<div style="clear:both;"></div>
			</div>
			
			<!-- start images -->
			<?php if( count($incident_photos) > 0 ) { ?>
				<div class="report-images" style="display:none;">
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
				<div class="report-video" style="display:none;">
					<?php
						// embed the video codes
						foreach( $incident_videos as $incident_video) {
							$videos_embed->embed($incident_video,'');
						}
					?>
				</div>
			<?php } ?>
			<!-- end videos -->
		
		</div>
		
		<script type="text/javascript">
			
			$('#showmap').click(function() {
				$('.report-images').slideUp('slow', function() {
					$('#imagesli').removeClass('report-tab-selected');
				});
				$('.report-video').slideUp('slow', function() {
					$('#videoli').removeClass('report-tab-selected');
				});
				$('.report-map').show('slow', function() {
					$('#mapli').addClass('report-tab-selected');
				});
			});
			
			$('#showimages').click(function() {
				$('.report-map').slideUp('slow', function() {
					$('#mapli').removeClass('report-tab-selected')
				});
				$('.report-video').slideUp('slow', function() {
					$('#videoli').removeClass('report-tab-selected');
				});
				$('.report-images').show('slow', function() {
					$('#imagesli').addClass('report-tab-selected');
				});
			});
			
			$('#showvideo').click(function() {
				$('.report-map').slideUp('slow', function() {
					$('#mapli').removeClass('report-tab-selected')
				});
				$('.report-images').slideUp('slow', function() {
					$('#imagesli').removeClass('report-tab-selected');
				});
				$('.report-video').show('slow', function() {
					$('#videoli').addClass('report-tab-selected');
				});
			});
			
			$('#showtallermap').click(function() {
				$('.map-holder').css("height","600px");
				$('#showtallermap').hide(0);
				$('#showshortermap').show(0);
			});
			
			$('#showshortermap').click(function() {
				$('.map-holder').css("height","200px");
				$('#showshortermap').hide(0);
				$('#showtallermap').show(0);
			});

		</script>

		<div class="report-additional-reports">
			<h5><?php echo Kohana::lang('ui_main.additional_reports');?></h5>
			<table cellpadding="0" cellspacing="0">
				<tr class="title">
					<th class="w-01"><?php echo Kohana::lang('ui_main.title');?></th>
					<th class="w-02"><?php echo Kohana::lang('ui_main.location');?></th>
					<th class="w-03"><?php echo Kohana::lang('ui_main.date');?></th>
				</tr>
				<?php foreach($incident_neighbors as $neighbor) { ?>
				<tr>
					<td class="w-01"><a href="<?php echo url::site(); ?>reports/view/<?php echo $neighbor->id; ?>"><?php echo $neighbor->incident_title; ?></a></td>
					<td class="w-02" style="width:300px;"><?php echo $neighbor->location->location_name; ?></td>
					<td class="w-03" style="padding-left:5px;" nowrap><?php echo date('M j Y', strtotime($neighbor->incident_date)); ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>

	</div>
	
	<div style="clear:both;"></div>
	
	
	
	
</div>