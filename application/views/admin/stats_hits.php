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
    <h3>Visitors Summary</h3>
    <div id="time-period-selector">
    	
        <p>Choose a date range: <a href="#">1 MO</a> <a href="#">3 MO</a> <a href="#">6 MO</a> <input type="text" class="dp" value="datepicker" /> - <input type="text" value="datepicker" class="dp" /> <input type="button" value="Go &rarr;" class="button" /> </p>
        
    </div>
    
    <div class="chart-holder">
    	<p><strong>Width:</strong> 100% (904px)<br /><strong>Height:</strong> 300px</p>
    </div>
    <div class="stats-wrapper clearfix">        
        <div class="statistic first">
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
                    <table class="table-graph horizontal-bar">
                        <tr>
                            <td class="hbItem">November 20, 2009</td>
                            <td class="hbDesc"><span style="width:14%" class="stat-bar">&nbsp;</span><span class="stat-percentage">%14 (23)</span></td>
                        </tr>
                        <tr>
                            <td class="hbItem">November 19, 2009</td>
                            <td class="hbDesc"><span style="width:10%" class="stat-bar">&nbsp;</span><span class="stat-percentage">%10 (20)</span></td>
                        </tr>
                        <tr>
                            <td class="hbItem">November 18, 2009</td>
                            <td class="hbDesc"><span style="width:20%" class="stat-bar">&nbsp;</span><span class="stat-percentage">%20 (40)</span></td>
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
    <div class="two-col tc-right glossary">
    	<h4>Glossary</h4>
        <div class="terms">
            <p><strong>Unique Visitors:</strong> The number of unique individuals (identified by ip address) who have visited your site.</p>
            <p><strong>Visits:</strong> The total number of times  individuals (identified by ip address) have visited your site.</p>
            <p><strong>Pageviews:</strong> The total number of pages that visitors have viewed on your site.</p>
        </div>
    </div>
    <div style="clear:both;"></div>
    <br /><br />
    
		<?php echo $traffic_chart; ?>
        
        <?php
            $labels = array();
            foreach($raw_data as $label => $data_array) {
                echo "<div style=\"width:200px;float:left;\"><h3>$label</h3>";
                $data_array = array_reverse($data_array);
                foreach($data_array as $timestamp => $count) {
                    $date = date('M jS, Y',($timestamp/1000));
                    echo "$date: $count<br/>";
                }
                echo "</div>";
            }
            echo "<div style=\"clear:both;\"></div>";
        ?>
	</div>
</div>
<script type="text/javascript" language="javascript">

  	/*Not sure why this doesn't work... getting  weird JS error 
	//tabs
    $(".tabset a").click(function(){
    	//remove all the active states
    	$(".tab-box").removeClass("active-tab");
        
        //show the appropriate tab box
        $($(this).attr("href")).addClass("active-tab");
        
        //don't jump around on the page please
        return false;
    });*/
</script>