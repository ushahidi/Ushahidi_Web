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
	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Visitor Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> <a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> <a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a></h2>

	<div class="content-wrap">
	<h3>Country Breakdown</h3>
	
	<div id="time-period-selector">
		<p>
			<form method="get" action="<?php print url::base() ?>admin/stats/country/" style="display: inline;">
				Choose a date range: <a href="<?php print url::base() ?>admin/stats/country/?range=30">1 MO</a> <a href="<?php print url::base() ?>admin/stats/country/?range=90">3 MO</a> <a href="<?php print url::base() ?>admin/stats/country/?range=180">6 MO</a>
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
			<h3>Error!</h3>
			<ul><li><?php echo $failure; ?></li></ul>
		</div>
	<?php } ?>
	</div>

	<div class="stats-wrapper clearfix">
		<div class="statistic first">
			<h4>Countries</h4>
			<p><?php echo $num_countries; ?></p>
		</div>
		<div class="statistic">
			<h4>Unique Visitors</h4>
			<p><?php echo $uniques; ?></p>
		</div>
		<div class="statistic">
			<h4>Visits</h4>
			<p><?php echo $visits; ?></p>
		</div>
		<div class="statistic">
			<h4>Pageviews</h4>
			<p><?php echo $pageviews; ?></p>
		</div>
	</div>

	<!-- Left Column -->
	<div class="two-col tc-left">
		<div class="tabs">
			<!-- tabset -->
			<ul class="tabset">
				<li><a class="active">Unique Visitors</a></li>
			</ul>
			<div class="tab-boxes">

				<div class="tab-box active-tab" id="unique-visitors">
					<table class="table-graph generic-data">
						<tr>
							<th class="gdItem">Country</th>
							<th class="gdDesc">Unique Visitors</th>
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