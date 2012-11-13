<?php echo form::open(NULL, array('method' => 'get', 'class' => 'report-search-form')); ?>
<h4><?php echo Kohana::lang('ui_main.filter_reports_by'); ?></h4>
<?php
	$search = array();
?>
<div class="row category-row">
<?php
	// Category
	echo form::label('c', Kohana::lang('ui_main.category'));
	echo category::form_tree('c', $categories, 1, TRUE, TRUE);
?>
</div>
<div class="row location-row">
<?php
	// Location
	echo form::label('location_filter', Kohana::lang('ui_main.location'));
	echo form::checkbox('location_filter',1,$location_filter);
	echo $alert_radius_view;
?>
</div>
<div class="row">
<?php
	// date range
	echo form::label('from', Kohana::lang('ui_main.from'), ' class="fixw"');
	echo form::input('from',date('M d, Y', $date_from));
	echo form::label('to', Kohana::lang('ui_main.to'), ' class="wrapped"');
	echo form::input('to',date('M d, Y', $date_to));
?>
</div>
<div class="row">
<?php
	// Type/mode
	echo form::label('mode', Kohana::lang('ui_main.type'), ' class="fixw"');
	echo "<label class='wrapped'>".form::checkbox('mode[]',1, in_array(1, $mode)) . Kohana::lang('ui_main.web') . "</label>";
	echo "<label class='wrapped'>".form::checkbox('mode[]',2, in_array(2, $mode)) . Kohana::lang('ui_main.sms') . "</label>";
	echo "<label class='wrapped'>".form::checkbox('mode[]',3, in_array(3, $mode)) . Kohana::lang('ui_main.email') . "</label>";
	echo "<label class='wrapped'>".form::checkbox('mode[]',4, in_array(4, $mode)) . Kohana::lang('ui_main.twitter') . "</label>";
?>
</div>
<div class="row">
<?php
	// Media
	echo form::label('m', Kohana::lang('ui_main.media'), ' class="fixw"');
	echo "<label class='wrapped'>".form::checkbox('m[]',1, in_array(1, $media)) . Kohana::lang('ui_main.photos') . "</label>";
	echo "<label class='wrapped'>".form::checkbox('m[]',2, in_array(2, $media)) . Kohana::lang('ui_main.video') . "</label>";
	echo "<label class='wrapped'>".form::checkbox('m[]',4, in_array(4, $media)) . Kohana::lang('ui_main.reports_news') . "</label>";
?>
</div>
<div class="row">
<?php
	// Verification
	echo form::label('a', Kohana::lang('ui_main.approved'), ' class="fixw"');
	echo "<label class='wrapped'>".form::radio('a',1, $approved == 1) . Kohana::lang('ui_main.yes') . "</label>";
	echo "<label class='wrapped'>".form::radio('a',0, $approved == 0) . Kohana::lang('ui_main.no') . "</label>";
	echo "<label class='wrapped'>".form::radio('a','all', $approved == 'all') . Kohana::lang('ui_main.all') . "</label>";
?>
</div>
<div class="row">
<?php
	// Approved
	echo form::label('v', Kohana::lang('ui_main.verified'), ' class="fixw"');
	echo "<label class='wrapped'>".form::radio('v',1, $verified == 1) . Kohana::lang('ui_main.yes') . "</label>";
	echo "<label class='wrapped'>".form::radio('v',0, $verified == 0) . Kohana::lang('ui_main.no') . "</label>";
	echo "<label class='wrapped'>".form::radio('v','all', $verified == 'all') . Kohana::lang('ui_main.all') . "</label>";
?>
</div>
<div class="row">
<?php
	// Text
	echo form::label('k', Kohana::lang('ui_main.keywords'), ' class="fixw"');
	echo form::input('k',$keywords);
?>
</div>
<div class="row">
<?php
	echo form::hidden('status', 'search');
	echo form::input(array('type' => 'hidden', 'name' => 'start_loc[0]', 'value' => floatval($start_loc[0]), 'class' => 'search_lat'));
	echo form::input(array('type' => 'hidden', 'name' => 'start_loc[1]', 'value' => floatval($start_loc[1]), 'class' => 'search_lon'));
	echo form::submit('submit', Kohana::lang('ui_main.search'));
?>
</div>
<?php echo form::close(); ?>