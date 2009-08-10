<?php
/**
 * Main view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */
?>

<div id="content">
  <div class="content-bg">
    <!-- start map and media filter -->
    <div class="big-block">
      <div class="big-block-top">
        <div class="big-block-bottom">
          <div class="big-map-block">
            <div class="filter">
              <strong><?php echo Kohana::lang('ui_main.media_filter'); ?></strong>
              <ul>
                <li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
                <li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
                <li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
                <li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
                <li><a id="media_0" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
              </ul>
            </div>
            <div id="map" class="map-holder"></div>
            <div class="slider-holder">
              <form action="#">
                <input type="hidden" value="0" name="currentCat" id="currentCat">
                  <fieldset>
                    <div class="play"><a href="#" id="playTimeline">PLAY</a></div>
					<label for="startDate">From:</label>
                    <select name="startDate" id="startDate">
                      <?php echo $startDate; ?>
                    </select>

                    <label for="endDate">To:</label>
                    <select name="endDate" id="endDate">
                      <?php echo $endDate; ?>
                    </select>
                  </fieldset>
                </form>
              </div>
              <div id="graph" class="graph-holder"></div>
            </div>

            <div class="category">
              <strong class="title">CATEGORY FILTER</strong>
              <div class="grey-box">
                <div class="grey-box-bg">
                  <ul>
                    <li><a class="active" id="cat_0" href="#"><div class="swatch" style="background-color:#<?php echo $default_map_all;?>"></div><div class="float:left">All Categories</div></a></li>
                    <?php
		      foreach ($categories as $category => $category_info)
		      {
                          $category_title = $category_info[0];
                          $category_color = $category_info[1];
						echo '<li><a href="#" id="cat_'. $category .'"><div class="swatch" style="background-color:#'.$category_color.'"></div>
							<div>'.$category_title.'</div></a></li>';
			   }
		    		?>
                  </ul>
                </div>
              </div>
			
			<?php
			if ($shares)
			{ ?>
			    <div class="category" style="margin-top:20px;">
	              <strong class="title">OTHER USHAHIDI INSTANCES</strong>
	              <div class="grey-box">
	                <div class="grey-box-bg">
	                  <ul>
						<?php
						foreach ($shares as $share => $share_info)
						{
							$sharing_site_name = $share_info[0];
							$sharing_color = $share_info[1];
							echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
							<div>'.$sharing_site_name.'</div></a></li>';
						}
						?>
	                  </ul>
	                </div>
	              </div>			
			<?php
			}
			?>

			  <div class="category" style="margin-top:20px;">
				<strong class="title">HOW TO REPORT</strong>
				<div class="grey-box">
					<div class="grey-box-bg">
						<ol> 
			            	<?php if (!empty($phone_array)) 
							{ ?><li>By sending a message to <?php foreach ($phone_array as $phone) {
								echo "<strong>". $phone ."</strong>";
								if ($phone != end($phone_array)) {
									echo " or ";
								}
							} ?></li><?php } ?>
			            	<?php if (!empty($report_email)) 
							{ ?><li>By sending an email to <a href="mailto:<?=$report_email?>"><?=$report_email?></a></li><?php } ?>
							<?php if (!empty($twitter_hashtag_array)) 
							{ ?><li>By sending a tweet with the hashtag/s <?php foreach ($twitter_hashtag_array 
								as $twitter_hashtag) {
								echo "<strong>". $twitter_hashtag ."</strong>";
								if ($twitter_hashtag != end($twitter_hashtag_array)) {
									echo " or ";
								}
							} ?></li><?php } ?>
			            	<li>By <a href="<?php echo url::base() . 'reports/submit/'; ?>">filling a form</a> at the website</li>
			           	</ol>					
					</div>
				</div>	
			  </div>
			  <div class="report-btns">
				<a class="btn-red" href="<?php echo url::base() . 'reports/submit/'; ?>"><span><?php echo Kohana::lang('ui_main.submit'); ?></span></a>
			  </div>			
            </div>
          </div>
        </div>
      </div>
      <!-- end map and media filter <> start incidents and news blocks -->
      <div class="blocks-holder">
        <div class="small-block incidents">
          <h3><?php echo Kohana::lang('ui_main.incidents_listed'); ?></h3>
          <div class="block-bg">
            <div class="block-top">
              <div class="block-bottom">
                <ul>
                  <li>
                    <ul class="title">
                      <li class="w-01"><?php echo Kohana::lang('ui_main.title'); ?></li>
                      <li class="w-02"><?php echo Kohana::lang('ui_main.location'); ?></li>
                      <li class="w-03"><?php echo Kohana::lang('ui_main.date'); ?></li>
                    </ul>
                  </li>
                  <?php
		    if ($total_items == 0)
                        {
		  ?>
                  <li>
                    <ul>
                      <li class="w-01">No Reports In The System</li>
                      <li class="w-02">&nbsp;</li>
                      <li class="w-03">&nbsp;</li>
                    </ul>
                  </li>
                  <?php
                  }
					foreach ($incidents as $incident)
                	{
                    	$incident_id = $incident->id;
						$incident_title = text::limit_chars($incident->incident_title, 40, '...', True);
                        $incident_date = $incident->incident_date;
                        $incident_date = date('M j Y', strtotime($incident->incident_date));
                        $incident_location = $incident->location->location_name;
		  		  ?>
                  <li>
                    <ul>
                      <li class="w-01">
                        <a href="<?php echo url::base() . 'reports/view/' . $incident_id; ?>">
                        <?php echo $incident_title ?></a></li>
                      <li class="w-02"><?php echo $incident_location ?></li>
                      <li class="w-03"><?php echo $incident_date; ?></li>
                    </ul>
                  </li>
                  <?php
					}
				?>
                </ul>
                <a class="btn-more" href="<?php echo url::base() . 'reports/'; ?>"><span>MORE</span></a>
              </div>
            </div>
          </div>
        </div>
        <div class="small-block news">
          <h3><?php echo Kohana::lang('ui_main.official_news'); ?></h3>
          <div class="block-bg">
            <div class="block-top">
              <div class="block-bottom">
                <ul>
	                <li>
	                  <ul class="title">
	                    <li class="w-01"><?php echo Kohana::lang('ui_main.title'); ?></li>
	                    <li class="w-02"><?php echo Kohana::lang('ui_main.source'); ?></li>
	                    <li class="w-03"><?php echo Kohana::lang('ui_main.date'); ?></li>
	                  </ul>
	                </li>
					<?php
					foreach ($feeds as $feed)
					{
						$feed_id = $feed->id;
						$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
						$feed_link = $feed->item_link;
						$feed_date = date('M j Y', strtotime($feed->item_date));
						$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
						?>
						<li>
							<ul>
								<li class="w-01">
								<a href="<?php echo $feed_link; ?>" target="_blank">
								<?php echo $feed_title ?></a></li>
								<li class="w-02"><?php echo $feed_source; ?></li>
								<li class="w-03"><?php echo $feed_date; ?></li>
							</ul>
						</li>
						<?php
					}
					?>
                </ul>
                <a class="btn-more" href="feeds"><span>MORE</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end start incidents and news blocks -->
    </div>
  </div>
