<?php
/**
 * Layout for the admin interface.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<title><?php echo html::specialchars($site_name) ?></title>
	<?php
	echo html::stylesheet(url::file_loc('css').'media/css/admin/all', '', TRUE);
	echo html::stylesheet(url::file_loc('css').'media/css/jquery-ui-themeroller', '', TRUE);
	echo "<!--[if lt IE 7]>".
		html::stylesheet(url::file_loc('css').'media/css/ie6', '', TRUE)
		."<![endif]-->";

	// Load OpenLayers
	if ($map_enabled)
	{
		echo html::script(url::file_loc('js').'media/js/OpenLayers', TRUE);
		echo html::script(url::file_loc('js').'media/js/ushahidi', TRUE);
		echo $api_url . "\n";
		echo "<script type=\"text/javascript\">
			OpenLayers.ImgPath = '".url::file_loc('img').'media/img/openlayers/'."';
			</script>";
		echo html::stylesheet(url::file_loc('css').'media/css/openlayers','',TRUE);
	}

	// Load jQuery
	echo html::script(url::file_loc('js').'media/js/jquery', TRUE);
	echo html::script(url::file_loc('js').'media/js/jquery.form', TRUE);
	echo html::script(url::file_loc('js').'media/js/jquery.validate.min', TRUE);
	echo html::script(url::file_loc('js').'media/js/jquery.ui.min', TRUE);
	echo html::script(url::file_loc('js').'media/js/selectToUISlider.jQuery', TRUE);
	echo html::script(url::file_loc('js').'media/js/jquery.hovertip-1.0', TRUE);
	echo html::script(url::file_loc('js').'media/js/jquery.base64', TRUE);
	?>

	<?php if ($datepicker_enabled): ?>
	<script type="text/javascript">
		Date.dayNames = [
		    '<?php echo Kohana::lang('datetime.sunday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.monday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.tuesday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.wednesday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.thursday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.friday.full'); ?>',
		    '<?php echo Kohana::lang('datetime.saturday.full'); ?>'
		];
		Date.abbrDayNames = [
		    '<?php echo Kohana::lang('datetime.sunday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.monday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.tuesday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.wednesday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.thursday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.friday.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.saturday.abbv'); ?>'
		];
		Date.monthNames = [
		    '<?php echo Kohana::lang('datetime.january.full'); ?>',
		    '<?php echo Kohana::lang('datetime.february.full'); ?>',
		    '<?php echo Kohana::lang('datetime.march.full'); ?>',
		    '<?php echo Kohana::lang('datetime.april.full'); ?>',
		    '<?php echo Kohana::lang('datetime.may.full'); ?>',
		    '<?php echo Kohana::lang('datetime.june.full'); ?>',
		    '<?php echo Kohana::lang('datetime.july.full'); ?>',
		    '<?php echo Kohana::lang('datetime.august.full'); ?>',
		    '<?php echo Kohana::lang('datetime.september.full'); ?>',
		    '<?php echo Kohana::lang('datetime.october.full'); ?>',
		    '<?php echo Kohana::lang('datetime.november.full'); ?>',
		    '<?php echo Kohana::lang('datetime.december.full'); ?>'
		];
		Date.abbrMonthNames = [
		    '<?php echo Kohana::lang('datetime.january.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.february.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.march.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.april.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.may.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.june.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.july.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.august.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.september.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.october.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.november.abbv'); ?>',
		    '<?php echo Kohana::lang('datetime.december.abbv'); ?>'
		];
		Date.firstDayOfWeek = 1;
		Date.format = 'mm/dd/yyyy';
	</script>

	<?php
		echo html::script(url::file_loc('js').'media/js/jquery.datePicker', TRUE);
		echo '<!--[if IE]>'.
			html::script(url::file_loc('js').'media/js/jquery.bgiframe.min', TRUE)
			.'<![endif]-->';
	?>
	<?php endif; ?>

	<?php
	echo html::stylesheet(url::file_loc('css').'media/css/jquery.hovertip-1.0', '', TRUE);

	echo "<script type=\"text/javascript\">
		$(function() {
			if($('.tooltip[title]') != null)
			$('.tooltip[title]').hovertip();
		});
	</script>";

	// Load Flot
	if ($flot_enabled)
	{
		echo html::script(url::file_loc('js').'media/js/jquery.flot', TRUE);
		echo html::script(url::file_loc('js').'media/js/excanvas.min', TRUE);
		echo html::script(url::file_loc('js').'media/js/timeline.js', TRUE);
	}

	// Load TreeView
	if ($treeview_enabled)
	{
		echo html::script(url::file_loc('js').'media/js/jquery.treeview');
		echo html::stylesheet(url::file_loc('css').'media/css/jquery.treeview');
	}

	// Load ProtoChart
	if ($protochart_enabled)
	{
		echo "<script type=\"text/javascript\">jQuery.noConflict()</script>";
		echo html::script(url::file_loc('js').'media/js/protochart/prototype', TRUE);
		echo '<!--[if IE]>';
		echo html::script(url::file_loc('js').'media/js/protochart/excanvas-compressed', TRUE);
		echo '<![endif]-->';
		echo html::script(url::file_loc('js').'media/js/protochart/ProtoChart', TRUE);
	}

	// Load Raphael
	if ($raphael_enabled)
	{
		// The only reason we include prototype is to keep the div element naming convention consistent
		//echo html::script(url::file_loc('js').'media/js/protochart/prototype', TRUE);
		echo html::script(url::file_loc('js').'media/js/raphael', TRUE);
		echo '<script type="text/javascript" charset="utf-8">';
		echo 'var impact_json = '.$impact_json .';';
		echo '</script>';
		echo html::script(url::file_loc('js').'media/js/raphael-ushahidi-impact', TRUE);
	}

	// Load ColorPicker
	if ($colorpicker_enabled)
	{
		echo html::stylesheet(url::file_loc('css').'media/css/colorpicker', '', TRUE);
		echo html::script(url::file_loc('js').'media/js/colorpicker', TRUE);
	}

	// Load jwysiwyg
	if ($editor_enabled)
	{
		if (Kohana::config("cdn.cdn_ignore_jwysiwyg") == TRUE) {
			echo html::script(url::file_loc('ignore').'media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js', TRUE);
		} else {
			echo html::script(url::file_loc('js').'media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.js', TRUE);
		}
	}

	// Table Row Sort
	if ($tablerowsort_enabled)
	{
		echo html::script(url::file_loc('js').'media/js/jquery.tablednd_0_5', TRUE);
	}

	// JSON2 for IE+
	if ($json2_enabled)
	{
		echo html::script(url::file_loc('js').'media/js/json2', TRUE);
	}

	// Turn on picbox
	echo html::script(url::file_loc('js').'media/js/picbox', TRUE);
	echo html::stylesheet(url::file_loc('css').'media/css/picbox/picbox');

	//Turn on jwysiwyg
	echo html::stylesheet(url::file_loc('css').'media/js/jwysiwyg/jwysiwyg/jquery.wysiwyg.css');

	// Header Nav
	echo html::script(url::file_loc('js').'media/js/global', TRUE);
	echo html::stylesheet(url::file_loc('css').'media/css/global','',TRUE);

	// Render CSS and Javascript Files from Plugins
	echo plugin::render('stylesheet');
	echo plugin::render('javascript');

	// Action::header_scripts_admin - Additional Inline Scripts
	Event::run('ushahidi_action.header_scripts_admin');
	?>
	<script type="text/javascript" charset="utf-8">
		<?php echo $js . "\n"; ?>
		function info_search(){
			$("#info-search").submit();
		}
		function show_addedit(toggle){
			var addEditForm = $("#addedit");
			if (toggle) {
				addEditForm.toggle(400);
			} else {
				addEditForm.show(400);
			}
			// Clear fields, but not buttons or the CSRF token.
			$(':input','#addedit')
			 .not(':button, :submit, :reset, #action, :checkbox, [name="form_auth_token"]')
			 .val('')
			 .removeAttr('selected');
			
			// Reset checkbox separately to avoid wiping its value
			$(':checkbox','#addedit').removeAttr('checked');
				
			$("a.add").focus();
		}
		<?php if ($form_error): ?>
			$(document).ready(function() { $("#addedit").show(); });
		<?php endif; ?>
	</script>

	<?php echo $header_block; ?>
</head>
<body>

	<?php
		echo $header_nav;

		// Action::admin_header_top_left - Admin Header Menu
		Event::run('ushahidi_action.admin_header_top_left');

		// Action::admin_secondary_header_bar - Admin Secondary Menu
		Event::run('ushahidi_action.admin_secondary_header_bar');
	?>

	<div class="holder">
		<!-- header -->
		<div id="header">

			<!-- info-nav -->
			<div class="info-nav">
				<h3><?php echo Kohana::lang('ui_admin.get_help');?></h3>
				<ul>
					<li ><a href="http://wiki.ushahidi.com/"><?php echo Kohana::lang('ui_admin.wiki');?></a></li>
					<li><a href="http://ushahidi.com/community_resources/"><?php echo Kohana::lang('ui_admin.faqs');?></a></li>
					<li><a href="http://forums.ushahidi.com/"><?php echo Kohana::lang('ui_admin.forum');?></a></li>
				</ul>
				
				<!-- languages -->
				<?php echo $languages; ?>
				<!-- / languages -->
				<div class="info-search">
					<?php echo form::open('admin/reports', array('method' => 'get', 'id' => 'info-search')); ?>
					<input type="text" name="k" class="info-keyword" value=""> 
					<a href="javascript:info_search();" class="btn">
						<?php echo Kohana::lang('ui_admin.search_reports');?>
					</a>
					<?php echo form::close(); ?>
				</div>
				<div style="clear:both;"></div>
				<div class="info-buttons">
					<a class="button" href="<?php echo url::site().'admin/manage/publiclisting'; ?>">
						<?php echo Kohana::lang('ui_admin.manage_public_listing'); ?>
					</a>
				</div>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<?php foreach ($main_tabs as $page => $tab_name): ?>
						<li>
							<a href="<?php echo url::site(); ?>admin/<?php echo $page; ?>" <?php if($this_page==$page) echo 'class="active"' ;?>>
								<?php echo $tab_name; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<!-- sub-nav -->
				<ul class="sub-nav">
					<?php foreach ($main_right_tabs as $page => $tab_name): ?>
						<li>
							<a href="<?php echo url::site(); ?>admin/<?php echo $page; ?>" <?php if($this_page==$page) echo 'class="active"' ;?>>
								<?php echo $tab_name; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<!-- content -->
		<div id="content">
			<div class="bg">
				<?php print $content; ?>
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="holder">
			<strong>
				<a href="http://www.ushahidi.com" target="_blank" title="Ushahidi Platform" alt="Ushahidi Platform">
                	<sup><?php echo Kohana::config('settings.ushahidi_version');?></sup>
            	</a>
			</strong>
		</div>
	</div>
<?php echo $footer_block; ?>
</body>
</html>
