<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<style type="text/css">
#kohana-profiler
{
	font-family: Monaco, 'Courier New';
	background-color: #F8FFF8;
	margin-top: 20px;
	clear: both;
	padding: 10px 10px 0;
	border: 1px solid #E5EFF8;
	text-align: left;
}
#kohana-profiler pre
{
	margin: 0;
	font: inherit;
}
#kohana-profiler .kp-meta
{
	margin: 0 0 10px;
	padding: 4px;
	background: #FFF;
	border: 1px solid #E5EFF8;
	color: #A6B0B8;
	text-align: center;
}
<?php echo $styles ?>
</style>
<div id="kohana-profiler">
<?php
foreach ($profiles as $profile)
{
	echo $profile->render();
}
?>
<p class="kp-meta">Profiler executed in <?php echo number_format($execution_time, 3) ?>s</p>
</div>