<?php 
/**
 * Feedback view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<div class="bg">
	<h2><?php echo $title; ?> <a href="<?php print url::site() ?>admin/stats/hits"><?php echo Kohana::lang('stats.visitor_summary');?></a> <a href="<?php print url::site() ?>admin/stats/country"><?php echo Kohana::lang('stats.country_breakdown');?></a> <a href="<?php print url::site() ?>admin/stats/reports"><?php echo Kohana::lang('stats.report_stats');?></a> <a href="<?php print url::site() ?>admin/stats/impact"><?php echo Kohana::lang('stats.category_impact');?></a> <a href="<?php print url::site() ?>admin/stats/punchcard"><?php echo Kohana::lang('stats.report_punchcard');?></a></h2>

	<div class="content-wrap">
	<h3><?php echo Kohana::lang('stats.country_breakdown');?></h3>
	
	<div id="time-period-selector">
		<p>
			<form method="get" action="<?php print url::base() ?>admin/stats/country/" style="display: inline;">
				<?php echo Kohana::lang('stats.choose_date_range');?>: <a href="<?php print url::site() ?>admin/stats/country/?range=30"><?php echo Kohana::lang('stats.time_range_1');?></a> <a href="<?php print url::site() ?>admin/stats/country/?range=90"><?php echo Kohana::lang('stats.time_range_2');?></a> <a href="<?php print url::site() ?>admin/stats/country/?range=180"><?php echo Kohana::lang('stats.time_range_3');?></a>  <a href="<?php print url::site() ?>admin/stats/country/"><?php echo Kohana::lang('stats.time_range_all');?></a>
				<input type="text" class="dp" name="dp1" id="dp1" value="<?php echo $dp1; ?>" />&nbsp;&nbsp;-&nbsp;&nbsp; 
				<input type="text" class="dp" name="dp2" id="dp2" value="<?php echo $dp2; ?>" /> 
				<input type="hidden" name="range" value="<?php echo $range; ?>" />
				<input type="submit" value="Go &rarr;" class="button" />
			</form>
		</p>
	</div>

	<div class="chart-holder" style="height:220px;text-align:center;">
	<img src="<?php echo $visitor_map; ?>" />
	<?php if($failure != ''){ ?>
		<div class="red-box">
			<h3><?php echo Kohana::lang('stats.error');?></h3>
			<ul><li><?php echo $failure; ?></li></ul>
		</div>
	<?php } ?>
	</div>

	<div class="stats-wrapper clearfix">
		<div class="statistic first">
			<h4><?php echo Kohana::lang('stats.countries');?></h4>
			<p><?php echo $num_countries; ?></p>
		</div>
		<div class="statistic">
			<h4><?php echo Kohana::lang('stats.unique_visitors');?></h4>
			<p><?php echo $uniques; ?></p>
		</div>
		<div class="statistic">
			<h4><?php echo Kohana::lang('stats.visits');?></h4>
			<p><?php echo $visits; ?></p>
		</div>
		<div class="statistic">
			<h4><?php echo Kohana::lang('stats.pageviews');?></h4>
			<p><?php echo $pageviews; ?></p>
		</div>
	</div>

	<!-- Left Column -->
	<div class="two-col tc-left">
		<div class="tabs">
			<!-- tabset -->
			<ul class="tabset">
				<li><a class="active"><?php echo Kohana::lang('stats.unique_visitors');?></a></li>
			</ul>
			<div class="tab-boxes">

				<div class="tab-box active-tab" id="unique-visitors">
					<table class="table-graph generic-data">
						<tr>
							<th class="gdItem"><?php echo Kohana::lang('stats.country');?></th>
							<th class="gdDesc"><?php echo Kohana::lang('stats.unique_visitors');?></th>
						</tr>
						<?php
						foreach($countries as $name => $data){
							?>
							<tr>
							<td class="gdItem"><img class="flag" src="<?php echo $data['icon']; ?>"/> <?php echo $name; ?></td>
							<td class="gdDesc"><?php echo $data['count']; ?></td>
							</tr>
							<?php
						}
						?>
					</table>
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
	</div>

</div>