<?php 
/**
 * Feedback view page.
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
	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Hit Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> <a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> <a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a></h2>
	
	<div class="content-wrap">
    <h3>Country Breakdown</h3>
    <div id="time-period-selector">
    	
        <p>Choose a date range: <a href="#">1 MO</a> <a href="#">3 MO</a> <a href="#">6 MO</a> <input type="text" class="dp" value="datepicker" /> - <input type="text" value="datepicker" class="dp" /> <input type="button" value="Go &rarr;" class="button" /> </p>
        
    </div>
    
    <div class="chart-holder">
    <img src="<?php echo $visitor_map; ?>" />
    </div>
    <div class="stats-wrapper clearfix">
        <div class="statistic first">
            <h4>Countries</h4>
            <p>34</p>
        </div>
        <div class="statistic">
        	<h4>Unique Visitors</h4>
            <p>342</p>
        </div>
        <div class="statistic">
            <h4>Visits</h4>
            <p>423</p>
        </div>
        <div class="statistic">
            <h4>Pageviews</h4>
            <p>1217</p>
        </div>
        
    </div>
    
    <!-- Left Column -->
    <div class="two-col tc-left">
		<div class="tabs">
            <!-- tabset -->
            <ul class="tabset">
                <li><a class="active" href="#unique-visitors">Unique Visitors</a></li>
                <li><a href="#visits">Visits</a></li>
                <li><a href="#pageviews">Pageviews</a></li>
            </ul>
            <div class="tab-boxes">
                
                <div class="tab-box active-tab" id="unique-visitors">
                    <table class="table-graph generic-data">
                        <tr>
                        	<th class="gdItem">Country</th>
                            <th class="gdDesc">Unique Visitors</th>
                        </tr>
                        <tr>
                            <td class="gdItem"><img class="flag" src="http://tracker.ushahidi.com/piwik/plugins/UserCountry/flags/us.png"/> United States</td>
                            <td class="gdDesc">90</td>
                        </tr>
                        <tr>
                            <td class="gdItem"><img class="flag" src="http://tracker.ushahidi.com/piwik/plugins/UserCountry/flags/fr.png"/> France</td>
                            <td class="gdDesc">45</td>
                        </tr>
                        <tr>
                            <td class="gdItem"><img class="flag" src="http://tracker.ushahidi.com/piwik/plugins/UserCountry/flags/es.png"/> Mexico</td>
                            <td class="gdDesc">37</td>
                        </tr>
                        <tr>
                            <td class="gdItem"><img class="flag" src="http://tracker.ushahidi.com/piwik/plugins/UserCountry/flags/ke.png"/> Kenya</td>
                            <td class="gdDesc">15</td>
                        </tr>
                    </table>
                </div>
                
                <div class="tab-box" id="visits">
                    <p><strong>visits</strong></p>
                </div>
                
                <div class="tab-box" id="pageviews">
                    <p><strong>pageviews</strong></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="two-col tc-right">
    	<!-- Nothing here yet -->
    </div>
    <div style="clear:both;"></div>
    <br /><br />
    
		
		<table style="width:200px;">
		<tr><th>Country</td><th>Uniques</td></tr>
		<?php
			foreach($countries as $date => $country){
				echo '<tr><td colspan="2"><br/>'.$date.'</td></tr>';
				foreach($country as $code => $arr) {
					echo '<tr><td><img src="'.$arr['logo'].'" /> '.$arr['label'].'</td><td>'.$arr['uniques'].'</td></tr>';
				}
			}
		?>
		</table>
	</div>
	
</div>