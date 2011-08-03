<?php
/**
 * View file for updating the reports display
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team - http://www.ushahidi.com
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		<!-- Top reportbox section-->
		<div class="rb_nav-controls r-5">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<ul class="link-toggle report-list-toggle lt-icons-and-text">
							<li class="active"><a href="#rb_list-view" class="list">List</a></li>
							<li><a href="#rb_map-view" class="map">Map</a></li>
						</ul>
					</td>
					<td><?php echo $pagination; ?></td>
					<td><?php echo $stats_breadcrumb; ?></td>
					<td class="last">
						<ul class="link-toggle lt-icons-only">
							<?php //@todo Toggle the status of these links depending on the current page ?>
							<li><a href="#" class="prev" id="page_<?php echo $previous_page; ?>"><?php echo Kohana::lang('ui_main.previous'); ?></a></li>
							<li><a href="#" class="next" id="page_<?php echo $next_page; ?>"><?php echo Kohana::lang('ui_main.next'); ?></a></li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<!-- /Top reportbox section-->
		
		<!-- Report listing -->
		<div class="r_cat_tooltip"><a href="#" class="r-3"></a></div>
		<div class="rb_list-and-map-box">
			<div id="rb_list-view">
			<?php
				foreach ($incidents as $incident)
				{
					$incident = ORM::factory('incident', $incident->incident_id);
					$incident_id = $incident->id;
					$incident_title = $incident->incident_title;
					$incident_description = $incident->incident_description;
					//$incident_category = $incident->incident_category;
					// Trim to 150 characters without cutting words
					// XXX: Perhaps delcare 150 as constant

					$incident_description = text::limit_chars(strip_tags($incident_description), 140, "...", true);
					$incident_date = date('H:i M d, Y', strtotime($incident->incident_date));
					//$incident_time = date('H:i', strtotime($incident->incident_date));
					$location_id = $incident->location_id;
					$location_name = $incident->location->location_name;
					$incident_verified = $incident->incident_verified;

					if ($incident_verified)
					{
						$incident_verified = '<span class="r_verified">'.Kohana::lang('ui_main.verified').'</span>';
						$incident_verified_class = "verified";
					}
					else
					{
						$incident_verified = '<span class="r_unverified">'.Kohana::lang('ui_main.unverified').'</span>';
						$incident_verified_class = "unverified";
					}

					$comment_count = $incident->comment->count();

					$incident_thumb = url::file_loc('img')."media/img/report-thumb-default.jpg";
					$media = $incident->media;
					if ($media->count())
					{
						foreach ($media as $photo)
						{
							if ($photo->media_thumb)
							{ // Get the first thumb
								$prefix = url::base().Kohana::config('upload.relative_directory');
								$incident_thumb = $prefix."/".$photo->media_thumb;
								break;
							}
						}
					}
				?>
				<div id="<?php echo $incident_id ?>" class="rb_report <?php echo $incident_verified_class; ?>">
					<div class="r_media">
						<p class="r_photo"> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>">
							<img src="<?php echo $incident_thumb; ?>" height="59" width="89" /> </a>
						</p>

						<!-- Only show this if the report has a video -->
						<p class="r_video" style="display:none;"><a href="#">Video</a></p>

						<!-- Category Selector -->
						<div class="r_categories">
							<h4><?php echo Kohana::lang('ui_main.categories'); ?></h4>
							<?php foreach ($incident->category as $category): ?>
								
								<?php // Don't show hidden categories ?>
								<?php if($category->category_visible == 0) continue; ?>
						
								<?php if ($category->category_image_thumb): ?>
									<?php $category_image = url::base().Kohana::config('upload.relative_directory')."/".$category->category_image_thumb; ?>
									<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>">
										<span class="r_cat-box"><img src="<?php echo $category_image; ?>" height="16" width="16" /></span> 
										<span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span>
									</a>
								<?php else:	?>
									<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>">
										<span class="r_cat-box" style="background-color:#<?php echo $category->category_color;?>;"></span> 
										<span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span>
									</a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="r_details">
						<h3><a class="r_title" href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>">
								<?php echo $incident_title; ?>
							</a>
							<a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>#discussion" class="r_comments">
								<?php echo $comment_count; ?></a> 
								<?php echo $incident_verified; ?>
							</h3>
						<p class="r_date r-3 bottom-cap"><?php echo $incident_date; ?></p>
						<div class="r_description"> <?php echo $incident_description; ?>  
						  <a class="btn-show btn-more" href="#<?php echo $incident_id ?>">More Info &raquo;</a> 
						  <a class="btn-show btn-less" href="#<?php echo $incident_id ?>">&laquo; Less Info</a> 
						</div>
						<p class="r_location"><a href="<?php echo url::site(); ?>reports/?l=<?php echo $location_id; ?>"><?php echo $location_name; ?></a></p>
					</div>
				</div>
			<?php } ?>
			</div>
			<div id="rb_map-view" style="display:none; width: 590px; height: 384px; border:1px solid #CCCCCC; margin: 3px auto;">
			</div>
		</div>
		<!-- /Report listing -->
		
		<!-- Bottom paginator -->
		<div class="rb_nav-controls r-5">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<ul class="link-toggle report-list-toggle lt-icons-and-text">
							<li class="active"><a href="#rb_list-view" class="list">List</a></li>
							<li><a href="#rb_map-view" class="map">Map</a></li>
						</ul>
					</td>
					<td><?php echo $pagination; ?></td>
					<td><?php echo $stats_breadcrumb; ?></td>
					<td class="last">
						<ul class="link-toggle lt-icons-only">
							<?php //@todo Toggle the status of these links depending on the current page ?>
							<li><a href="#" class="prev" id="page_<?php echo $previous_page; ?>"><?php echo Kohana::lang('ui_main.previous'); ?></a></li>
							<li><a href="#" class="next" id="page_<?php echo $next_page; ?>"><?php echo Kohana::lang('ui_main.next'); ?></a></li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<!-- /Bottom paginator -->
	        