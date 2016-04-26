<h3>
	<a href="#" class="small-link-button f-clear reset" onclick="removeParameterKey('plugin_actionable_filter', 'fl-actionable');">
		<?php echo Kohana::lang('ui_main.clear')?>
	</a>
	<a class="f-title" href="#"><?php echo Kohana::lang('actionable.actionable') ?></a>
</h3>
<div class="f-actionable">
	<ul class="filter-list fl-actionable">
		<li>
			<a href="#" id="filter_actionable_2">
				<!--span class="item-icon ic-unverified">&nbsp;</span-->
				<span class="item-title"><?php echo Kohana::lang('actionable.urgent'); ?></span>
			</a>
		</li>
		<li>
			<a href="#" id="filter_actionable_1">
				<!--span class="item-icon ic-verified">&nbsp;</span-->
				<span class="item-title"><?php echo Kohana::lang('actionable.actionable'); ?></span>
			</a>
		</li>
		<li>
			<a href="#" id="filter_actionable_0">
				<!--span class="item-icon ic-unverified">&nbsp;</span-->
				<span class="item-title"><?php echo Kohana::lang('actionable.not_actionable'); ?></span>
			</a>
		</li>
	</ul>
</div>
