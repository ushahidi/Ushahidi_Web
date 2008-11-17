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
                <li><a class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
                <li><a href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
                <li><a href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
                <li><a href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
                <li><a href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
              </ul>
            </div>
            <div id="map" class="map-holder"></div>
            <div class="slider-holder">
              <form action="#">
                <input type="hidden" value="0" name="currentCat" id="currentCat">
                  <fieldset>
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
                    <li><a class="active" id="cat_0" href="#"><span style="background:no-repeat url(<?php echo url::base() . 'swatch/?c=990000&w=16&h=16&.png' ?>); background-position:left center;">All Categories</span></a></li>
                    <?php
		      foreach ($categories as $category => $category_info)
		      {
                          $category_title = $category_info[0];
                          $category_color = $category_info[1];
												echo '<li><a href="#" id="cat_'. $category .'"><span style="background:no-repeat url('. url::base() . "swatch/?c=" . $category_color . "&w=16&h=16&.png" . '); background-position:left center;">' . $category_title . '</span></a></li>';
                      }
		    ?>
                  </ul>
                </div>
              </div>
              <div class="report-btns">
                <a class="btn-red" href="<?php echo url::base() . 'reports/submit/'; ?>"><span><?php echo Kohana::lang('ui_main.submit'); ?></span></a>
                <?php if (!empty($phone_array)) ?><a class="btn-grey" href="#"><span><?php echo Kohana::lang('ui_main.submit_sms'); ?></span></a>
              </div>
			  <?php if (!empty($phone_array)) { ?>
              <p><?php echo Kohana::lang('ui_main.submit_sms1'); ?>  
				<?php foreach ($phone_array as $phone) {
					echo "<strong>". $phone ."</strong>";
					if ($phone != end($phone_array)) {
						echo ", ";
					}
				} ?>
				 <?php echo Kohana::lang('ui_main.submit_sms2'); ?></p><?php } ?>
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
						$feed_source = "NEWS";
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
                <a class="btn-more" href="#"><span>MORE</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end start incidents and news blocks -->
    </div>
  </div>
