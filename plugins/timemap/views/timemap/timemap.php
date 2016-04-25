<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $site_name; ?> <?php echo Kohana::lang('timemap.timemap'); ?></title>
		<?php
		// System Files
		echo html::stylesheet($css_url."media/css/openlayers","",TRUE);
		echo html::script($js_url."media/js/OpenLayers", TRUE);
		echo "<script type=\"text/javascript\">OpenLayers.ImgPath = '".$js_url."media/img/openlayers/"."';</script>";
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>plugins/timemap/media/timemap/css/styles.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>plugins/timemap/media/timemap/css/nav.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>plugins/timemap/media/timemap/css/timemap.css" />
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/mxn/mxn.js?(openlayers)"></script>
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/timeline-2.3.0.js"></script>
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/timemap.js"></script>
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/json.js"></script>
		<script type="text/javascript" src="<?php echo url::base(); ?>plugins/timemap/media/timemap/js/progressive.js"></script>
		<script type="text/javascript">
			var tm;
			$(function() {
				tm = TimeMap.init({
					mapId: "map",               // Id of map div element (required)
					timelineId: "timeline",     // Id of timeline div element (required)
					options: {
						eventIconPath: "<?php echo url::base(); ?>plugins/timemap/media/timemap/images/"
					},
					datasets: [
						{
							title: "Timemap",
							theme: "green",
							type: "progressive",
							options: {
								// Data to be loaded in JSON from a remote URL
								type: "json",
								// url with start/end placeholders
								url: "<?php echo url::base(); ?>timemap/json?start=[start]&end=[end]&callback=?",
								start: "<?php echo $start_date; ?>",
								// lower cutoff date for data
								dataMinDate: "<?php echo $start_date; ?>",
								// four months in milliseconds
								interval: 86400000,
								// function to turn date into string appropriate for service
								formatDate: function(d) {
									return TimeMap.util.formatDate(d, 1);
								}
							}
						}
					],
					bandIntervals: "wk"
				});
			});
		 </script>
	</head>
	<body>
	<table>
		<tr style="height:40px;">
			<td class="header">
				<div class="title">
					<h1><?php echo $site_name; ?></h1>
					<span><?php echo $site_tagline; ?></span>
				</div>
				<div class="underlinemenu">
					<ul>
						<li><a href="<?php echo url::base(); ?>"><?php echo Kohana::lang('timemap.home'); ?></a></li>
						<li><a href="<?php echo url::site()."timemap/"; ?>" class="selected"><?php echo Kohana::lang('timemap.timemap'); ?></a></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr style="height:100%;" valign="top">
			<td style="height:100%;">
				<div id="timelinecontainer">
					<div id="timeline"></div>
				</div>
				<div id="mapcontainer">
					<div id="map"></div>
				</div>
			</td>
		</tr>
	</table>
	</body>
</html>
