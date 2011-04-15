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
	<h3><?php echo Kohana::lang('stats.report_punchcard');?></h3>
	
	<div>
		<br/><br/>
		<img src="<?php echo $chart_url; ?>" alt="Report Punchcard" />
	</div>
	


	<!-- Right Column -->
	<div class="two-col tc-right">
		<!-- Nothing here yet -->
	</div>
	<div style="clear:both;"></div>
	<br /><br />
	</div>

</div>