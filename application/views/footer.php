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
     

<!-- footer -->
<div id="footer" class="clearingfix">

  <div id="underfooter"></div>

  <!-- footer content -->
  <div class="rapidxwpr floatholder">

    <!-- footer credits -->
    <div class="footer-credits">
      Powered by <a href=""><img src="<?php echo url::base(); ?>/media/img/footer-logo.jpg" alt="Ushahidi" align="absmiddle" /></a>
    </div>
    <!-- / footer credits -->
    
    <!-- footer menu -->
    <div class="footermenu">
      <ul class="clearingfix">
					<li><a class="item1" href="<?php echo url::base() ?>"><?php echo Kohana::lang('ui_main.home'); ?></a></li>
					<li><a href="<?php echo url::base() . "reports/submit" ?>"><?php echo Kohana::lang('ui_main.report_an_incident'); ?></a></li>
					<li><a href="<?php echo url::base() . "alerts" ?>"><?php echo Kohana::lang('ui_main.alerts'); ?></a></li>
					<li><a href="<?php echo url::base() . "help" ?>"><?php echo Kohana::lang('ui_main.help'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.about'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.blog'); ?></a></li>
      </ul>
      Copyright &copy; 2009 Ushahidi.com. All Rights Reserved.
    </div>
    <!-- / footer menu -->
  </div>
  <!-- / footer content -->

</div>
<!-- / footer -->

</body>
</html>
