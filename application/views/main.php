<div id="content">
  <div class="content-bg">
    <!-- start map and media filter -->
    <div class="big-block">
      <div class="big-block-top">
        <div class="big-block-bottom">
          <div class="big-map-block">
            <div class="filter">
              <strong>MEDIA FILTER</strong>
              <ul>
                <li><a class="active" href="#"><span>Reports</span></a></li>
                <li><a href="#"><span>News</span></a></li>
                <li><a href="#"><span>Pictures</span></a></li>
                <li><a href="#"><span>Video</span></a></li>
                <li><a href="#"><span>All</span></a></li>
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
                    <li><a class="active" id="cat_0" href="#"><span style="background:no-repeat url(<?php echo url::base() . 'swatch/?c=ffffff&w=16&h=16&.png' ?>); background-position:left center;">All Categories</span></a></li>
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
                <a class="btn-red" href="<?php echo url::base() . 'reports/submit/'; ?>"><span>Submit an Incident!</span></a>
                <a class="btn-grey" href="#"><span>Submit via SMS</span></a>
              </div>
              <p>Send your SMS to <strong>6007</strong> on your phone</p>
            </div>
          </div>
        </div>
      </div>
      <!-- end map and media filter <> start incidents and news blocks -->
      <div class="blocks-holder">
        <div class="small-block incidents">
          <h3>Incidents <span>(from map above listed chronologically)</span></h3>
          <div class="block-bg">
            <div class="block-top">
              <div class="block-bottom">
                <ul>
                  <li>
                    <ul class="title">
                      <li class="w-01">TITLE</li>
                      <li class="w-02">LOCATION</li>
                      <li class="w-03">DATE</li>
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
          <h3>Official &amp; Mainstream News</h3>
          <div class="block-bg">
            <div class="block-top">
              <div class="block-bottom">
                <ul>
	                <li>
	                  <ul class="title">
	                    <li class="w-01">TITLE</li>
	                    <li class="w-02">SOURCE</li>
	                    <li class="w-03">DATE</li>
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
