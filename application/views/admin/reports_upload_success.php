<div class="bg">
	<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports/download">Download Reports</a><a href="<?php print url::base() ?>admin/reports">View Reports</a><a href="<?php print url::base() ?>admin/reports/edit">Create New Report</a></h2>
	
	<h3>Upload succesful</h3>
	   <p>Succesfully imported <?php echo $imported; ?> of <?php echo $rowcount; ?> incident reports.</p>

	
	<?php if(count($notices)){  ?>  
	<h3>Notices</h3>	
		<ul>
	<?php foreach($notices as $notice)  { ?>
	<li><?php echo $notice ?></li>

	<?php } }?>
	</ul>
	</div>
</div>