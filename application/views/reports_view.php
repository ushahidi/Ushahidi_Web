<div id="content">
  <div class="content-bg">
    <!-- start incident block -->
    <div class="big-block">
      <div class="big-block-top">
        <div class="big-block-bottom">
          <div class="incident-name">
            <h1><?php echo $incident_title; ?></h1>
            <ul>
              <li>
                <strong>LOCATION</strong>
                <p><?php echo $incident_location; ?></p>
              </li>
              <li>
                <strong>DATE</strong>
                <p><?php echo $incident_date; ?></p>
              </li>
              <li>
                <strong>TIME</strong>
                <p><?php echo $incident_time; ?></p>
              </li>
              <li>
                <strong>CATEGORY</strong>
                <p><a href="#">Internally Displaced People</a></p>
              </li>
              <li>
                <strong>ENTITY</strong>
                <p>N/A</p>
              </li>
              <li>
                <strong>VERIFIED</strong>
                <?php echo $incident_verified; ?>
              </li>
            </ul>
          </div>
          <div class="incident-map">
            <ul class="legend">
              <li class="ico-red">INCIDENT</li>
              <li class="ico-orange">NEARBY INCIDENTS</li>
            </ul>
            <div class="map-holder" id="map"></div>
          </div>
          <div class="report-description">
            <div class="title">
              <h2>Incident Report Description</h2>
              <a href="#"><span>+ add information</span></a>
            </div>
            <div class="orig-report">
              <div class="report">
                <h4>Original Report #1</h4>
                <p>We are here in Nairobi locked in the houses by police with guns. They have killed over ten people. People try to demonstrate, and a church is burning.</p>
                <a class="lnk" href="#"><span>credibility score?</span></a>
              </div>
            </div>
            <div class="orig-report">
              <div class="report">
                <h4>Original Report #2</h4>
                <p>34 women were going to be burned and 22 children by mungiki in a church near Ruiru. Help!</p>
                <a class="lnk" href="#"><span>credibility score?</span></a>
              </div>
              <div class="discussion">
                <h5>ADDITIONAL REPORTS AND DISCUSSION</h5>
                <div class="discussion-box">
                  <p>Final count of dead was 7, and another dozen were taken to the local hospitals for further aid.</p>
                  <a class="lnk" href="#"><span>credibility score?</span></a>
                </div>
                <div class="discussion-box">
                  <p>Final count of dead was 7, and another dozen were taken to the local hospitals for further aid.</p>
                  <a class="lnk" href="#"><span>credibility score?</span></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end incident block <> start other report -->
    <div class="blocks-holder">
      <!-- start images -->
      <div class="small-block images">
        <h3>Images</h3>
        <div class="block-bg">
          <div class="block-top">
            <div class="block-bottom">
              <div class="gallery">
                <div class="gal-nav">
                  <a class="btn-prev" href="#">PREV</a>
                  <div class="mask">
                    <ul>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                      <li><a href="#"><img src="<?php echo url::base() . 'media/img/'; ?>gal-small.gif" alt="" /></a></li>
                    </ul>
                  </div>
                  <a class="btn-next" href="#">PREV</a>
                </div>
                <div class="big-img">
                  <img src="<?php echo url::base() . 'media/img/'; ?>gal-big.gif" alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end images <> start side block -->
      <div class="side-block">
        <div class="small-block">
          <h3>Incident Report(s)</h3>
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
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
                      <li class="w-02">BBC</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
                      <li class="w-02">Yahoo!</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="small-block">
          <h3>Related Mainstream News of Incident</h3>
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
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
                      <li class="w-02">BBC</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
                      <li class="w-02">Yahoo!</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
                      <li class="w-02">BBC</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                  <li>
                    <ul>
                      <li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
                      <li class="w-02">Yahoo!</li>
                      <li class="w-03">18 Jan 2008</li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end side block -->
    </div>
    <!-- end incident block <> start other report -->
  </div>
</div>
