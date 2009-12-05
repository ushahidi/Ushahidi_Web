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

	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Visitor Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> <a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> <a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a></h2>
	
	<div class="content-wrap">
	<h3>Visitors Summary</h3>
	
	<div id="time-period-selector">
		<p>
			<form method="get" action="<?php print url::base() ?>admin/stats/hits/" style="display: inline;">
				Choose a date range: <a href="<?php print url::base() ?>admin/stats/hits/?range=30">1 MO</a> <a href="<?php print url::base() ?>admin/stats/hits/?range=90">3 MO</a> <a href="<?php print url::base() ?>admin/stats/hits/?range=180">6 MO</a>
				<input type="text" class="dp" name="dp1" id="dp1" value="<?php echo $dp1; ?>" />&nbsp;&nbsp;-&nbsp;&nbsp; 
				<input type="text" class="dp" name="dp2" id="dp2" value="<?php echo $dp2; ?>" /> 
				<input type="hidden" name="range" value="<?php echo $range; ?>" />
				<input type="hidden" name="active_tab" value="<?php echo $active_tab; ?>" /> 
				<input type="submit" value="Go &rarr;" class="button" />
			</form>
		</p>
	</div>

	<div class="chart-holder">
		<?php echo $traffic_chart; ?>
		<?php if($failure != ''){ ?>
			<div class="red-box">
				<h3>Error!</h3>
				<ul><li><?php echo $failure; ?></li></ul>
			</div>
		<?php } ?>
	</div>

	<div class="stats-wrapper clearfix">
		<div class="statistic first">
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
				<li><a <?php if($active_tab == 'uniques') echo 'class="active"'; ?> href="<?php print url::base() ?>admin/stats/hits/?range=<?php echo $range; ?>&dp1=<?php echo $dp1; ?>&dp2=<?php echo $dp2; ?>&active_tab=uniques">Unique Visitors</a></li>
				<li><a <?php if($active_tab == 'visits') echo 'class="active"'; ?> href="<?php print url::base() ?>admin/stats/hits/?range=<?php echo $range; ?>&dp1=<?php echo $dp1; ?>&dp2=<?php echo $dp2; ?>&active_tab=visits">Visits</a></li>
				<li><a <?php if($active_tab == 'pageviews') echo 'class="active"'; ?> href="<?php print url::base() ?>admin/stats/hits/?range=<?php echo $range; ?>&dp1=<?php echo $dp1; ?>&dp2=<?php echo $dp2; ?>&active_tab=pageviews">Pageviews</a></li>
			</ul>

			<div class="tab-boxes">
			<?php
			$labels = array();
			foreach($raw_data as $label => $data_array) {
				$activetabcss = '';
				if($label == $active_tab) $activetabcss = 'active-tab';
				?>
				<div class="tab-box <?php echo $activetabcss; ?>" id="<?php echo $label; ?>">
				<table class="table-graph horizontal-bar">
				<?php
				$data_array = array_reverse($data_array,true);
				foreach($data_array as $timestamp => $count) {
					$date = date('M jS, Y',($timestamp/1000));
					$percentage = 0;
					if($$label != 0) $percentage = round((($count / $$label) * 100),1);
					?>
					<tr>
						<td class="hbItem"><?php echo $date; ?></td>
						<td class="hbDesc"><span style="width:<?php echo $percentage; ?>%" class="stat-bar">&nbsp;</span><span class="stat-percentage"><?php echo $percentage; ?>% (<?php echo $count; ?>)</span></td>
					</tr>
					<?php
				}
				?>
				</table>
				</div>
				<?php
			}
			?>
			</div>

		</div>
	</div>

	<!-- Right Column -->
	<div class="two-col tc-right glossary">
		<h4>Glossary</h4>
		<div class="terms">
			<p><strong>Unique Visitors:</strong> The number of individuals coming to your instance; Unique Visitors are determined using cookies. In the case that a visitor does not have cookies enabled, they will be identified using a simple heuristic taking into account IP address, resolution, browser, plugins, OS, etc.</p>
			<p><strong>Visits:</strong> A visit is a record of a unique visitor coming to the site more than 30 minutes past his/her last pageview.</p>
			<p><strong>Pageviews:</strong> The total number of pages that visitors have viewed on your site.</p>
		</div>
	</div>
	<div style="clear:both;"></div>
	<br /><br />

	</div>
</div>
