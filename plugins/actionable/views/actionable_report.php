<div class="action-taken clearingfix">
	<?php if (!$action_taken && $actionable == 1) { ?>
    <div id="actionable-badge">
      <?php echo Kohana::lang('actionable.action_needed');?>
    </div>
	<?php }; ?>
  <?php if (!$action_taken && $actionable == 2) { ?>
    <div id="action-urgent-badge">
      <?php echo Kohana::lang('actionable.action_urgent');?>
    </div>
	<?php }; ?>
  <?php if ($action_taken) { ?>
    <div id="action-taken-badge">
      <?php echo Kohana::lang('actionable.action_taken');?>
    </div>
	<?php }; ?>
  <?php if ($action_summary) { ?>
		<div id="action-summary">
		<strong><?php echo Kohana::lang('actionable.summary');?>: </strong><?php echo $action_summary; ?>
		</div>
	<?php }; ?>
</div>