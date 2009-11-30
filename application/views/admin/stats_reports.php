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
	
    
    <div class="content-wrap clearfix">
        <h3>Reports Statistics</h3>
        <div id="time-period-selector">
            
            <p>Choose a date range: <a href="#">1 MO</a> <a href="#">3 MO</a> <a href="#">6 MO</a> <input type="text" class="dp" value="datepicker" /> - <input type="text" value="datepicker" class="dp" /> <input type="button" value="Go &rarr;" class="button" /> </p>
            
        </div>
		
        <!-- Left Column -->
        <div class="two-col tc-left reports-charts">
        	
            <h4>Reports Categories</h4>
            <p><img src="http://grab.by/M4d" alt="Categories" /></p>
      		
            <h4>Reports Status</h4>
            <p><img src="http://grab.by/M4f" alt="Status" /></p> 
        </div>
        
        <!-- Right Column -->
        <div class="two-col tc-right stats-sidebar">
        	<div class="stats-wrapper clearfix">
                <div class="statistic first">
                    <h4>Reports</h4>
                    <p>234</p>
                </div>
                <div class="statistic">
                    <h4>Categories</h4>
                    <p>7</p>
                </div>
                
            </div>
            <div style="clear:both;"></div>
            
        </div>
    </div>
    
	<?php echo $reports_chart; ?>
	<?php echo $report_status_chart; ?>

</div>

