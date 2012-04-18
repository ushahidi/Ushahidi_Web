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
	<h2><?php echo $title; ?> <a href="<?php print url::site() ?>admin/stats/hits"><?php echo Kohana::lang('stats.visitor_summary');?></a> <a href="<?php print url::site() ?>admin/stats/country"><?php echo Kohana::lang('stats.country_breakdown');?></a> <a href="<?php print url::site() ?>admin/stats/reports"><?php echo Kohana::lang('stats.report_stats');?></a> <a href="<?php print url::site() ?>admin/stats/impact"><?php echo Kohana::lang('stats.category_impact');?></a> <a href="<?php print url::site() ?>admin/stats/punchcard"><?php echo Kohana::lang('stats.report_punchcard');?></a></h2>
    
    <div class="content-wrap clearfix">
        <h3><?php echo Kohana::lang('stats.reports_statistics');?></h3>
        
        <div id="time-period-selector">
			<p>
				<?php echo form::open('admin/stats/reports/', array('method' => 'get', 'style' => "display: inline;")); ?>
					<?php echo Kohana::lang('stats.choose_date_range');?>: <a href="<?php print url::site() ?>admin/stats/reports/?range=30"><?php echo Kohana::lang('stats.time_range_1');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=90"><?php echo Kohana::lang('stats.time_range_2');?></a> <a href="<?php print url::site() ?>admin/stats/reports/?range=180"><?php echo Kohana::lang('stats.time_range_3');?></a> <a href="<?php print url::site() ?>admin/stats/reports/"><?php echo Kohana::lang('stats.time_range_all');?></a>
					<input type="text" class="dp" name="dp1" id="dp1" value="<?php echo $dp1; ?>" />&nbsp;&nbsp;-&nbsp;&nbsp; 
					<input type="text" class="dp" name="dp2" id="dp2" value="<?php echo $dp2; ?>" /> 
					<input type="hidden" name="range" value="<?php echo $range; ?>" />
					<input type="submit" value="Go &rarr;" class="button" />
				<?php echo form::close(); ?>
			</p>
		</div>
		
        <!-- Left Column -->
        <div class="two-col tc-left reports-charts">
        	
            <h4><?php echo Kohana::lang('stats.reports_categories');?></h4>
            <p>
            	<div style="float:left;"><?php echo $reports_chart; ?></div>
            	<div style="float:left;">
            		<table>
            			<?php
            			foreach($reports_per_cat as $category_id => $count){
	            			?>
	            			<tr>
	            				<td><div id="little-color-box" style="background-color:#<?php echo $category_data[$category_id]['category_color']; ?>">&nbsp;</div></td>
	            				<td><?php echo $category_data[$category_id]['category_title']; ?></td>
	            				<td style="padding-left:25px;"><?php echo $count; ?></td>
	            			</tr>
	            			<?php
            			}
            			?>
            		</table>
            	</div>
            	<div style="clear:both;"></div>
            </p>

            <h4><?php echo Kohana::lang('stats.reports_status');?></h4>
            <p>

            	<div style="float:left;"><?php echo $report_status_chart_ver; ?></div>
				<div style="float:left;">
					<table>
						<tr>
							<td><div id="little-color-box" style="background-color:#0E7800">&nbsp;</div></td>
							<td><?php echo Kohana::lang('stats.verified');?></td>
							<td style="padding-left:25px;"><?php echo $verified; ?></td>
						</tr>
						<tr>
							<td><div id="little-color-box" style="background-color:#FFCF00">&nbsp;</div></td>
							<td><?php echo Kohana::lang('stats.unverified');?></td>
							<td style="padding-left:25px;"><?php echo $unverified; ?></td>
						</tr>
					</table>
				</div>
            
				<div style="float:left;margin-left:100px;"><?php echo $report_status_chart_app; ?></div>
				<div style="float:left;">
					<table>
						<tr>
							<td><div id="little-color-box" style="background-color:#0E7800">&nbsp;</div></td>
							<td><?php echo Kohana::lang('stats.approved');?></td>
							<td style="padding-left:25px;"><?php echo $approved; ?></td>
						</tr>
						<tr>
							<td><div id="little-color-box" style="background-color:#FFCF00">&nbsp;</div></td>
							<td><?php echo Kohana::lang('stats.unapproved');?></td>
							<td style="padding-left:25px;"><?php echo $unapproved; ?></td>
						</tr>
					</table>
				</div>
				<div style="clear:both;"></div>

            </p> 
        </div>

        <!-- Right Column -->
        <div class="two-col tc-right stats-sidebar">
        	<div class="stats-wrapper clearfix">
                <div class="statistic first">
                    <h4><?php echo Kohana::lang('stats.reports');?></h4>
                    <p><?php echo $num_reports; ?></p>
                </div>
                <div class="statistic">
                    <h4><?php echo Kohana::lang('stats.categories');?></h4>
                    <p><?php echo $num_categories; ?></p>
                </div>
                
            </div>
            <div style="clear:both;"></div>
            
        </div>
    </div>

</div>

