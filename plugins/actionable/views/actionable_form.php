<!-- report is actionable -->
<div class="row">
	<h4 style="color: #009200;">
		<?php print form::checkbox('actionable', '1', $actionable); ?> Actionable?
		<span>Check if responders can act on this information.</span>
	</h4>
</div>
<!-- / report is actionable -->

<!-- report is acted on -->
<div class="row">
  <h4 style="color: #009200;">
    <?php print form::checkbox('action_taken', '1', $action_taken); ?> Action Taken
    <span>Check if action was taken and enter the action summary.</span>
  </h4>
  <textarea name="action_summary" id="action_summary" style=" height: 60px;"><?php echo $action_summary; ?></textarea>
</div>
<!-- / report is acted on -->