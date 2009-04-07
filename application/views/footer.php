<?php 
/**
 * Footer view page.
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
		<!-- end content block <> start footer block -->
		<div id="footer">
			<div class="footer-info">
				<ul>
					<li><a href="<?php echo url::base() ?>"><?php echo Kohana::lang('ui_main.home'); ?></a></li>
					<li><a href="<?php echo url::base() . "reports/submit" ?>"><?php echo Kohana::lang('ui_main.report_an_incident'); ?></a></li>
					<li><a href="<?php echo url::base() . "alerts" ?>"><?php echo Kohana::lang('ui_main.alerts'); ?></a></li>
					<li><a href="<?php echo url::base() . "help" ?>"><?php echo Kohana::lang('ui_main.help'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.about'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.blog'); ?></a></li>
					<li><a href="http://feedback.ushahidi.com/fillsurvey.php?sid=2&uid=<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank"><?php echo Kohana::lang('ui_main.feedback'); ?></a></li>
				</ul>
				<p><?php echo Kohana::lang('ui_main.copyright'); ?></p>
			</div>
			<strong class="f-logo"><a href="http://www.ushahidi.com">Ushahidi</a></strong>
			<img src="<?php echo $tracker_url; ?>" />
		</div>
		<!-- end footer block -->
	</div>
	<?php echo $google_analytics; ?>
	
	<!-- Task Scheduler -->
	<img src="<?php echo url::base() . 'scheduler'; ?>" height="1" width="1" border="0">
</body>
</html>