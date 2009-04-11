<?php 
/**
 * Report submit thanks view page.
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

<div id="content">
	<div class="content-bg">
		<!-- start alerts block -->
		<div class="big-block">
			<div class="big-block-top">
				<div class="big-block-bottom">
					<h1>Submit A New Report</h1>
					<!-- green-box -->
					<div class="green-box">
						<h3>Your Report has been submitted to our staff for review. We will get back to you shortly if necessary.</h3>

						<div class="thanks_msg"><a href="<?php echo
							url::base().'reports' ?>">Return to the reports page</a><br /><br /><br />
							Please give us feedback about your experience by clicking on the button below.<br /><br />
							<?php 
							print form::open('http://feedback.ushahidi.com/fillsurvey.php?sid=2', array('target'=>'_blank'));
							print form::hidden('alert_code', $_SERVER['SERVER_NAME']);
							print "&nbsp;&nbsp;";
							print form::submit('button', Kohana::lang('ui_main.feedback'), ' class=btn_gray ');
							print form::close();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>