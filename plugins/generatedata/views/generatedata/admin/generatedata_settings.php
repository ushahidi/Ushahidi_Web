<table style="width: 630px;" class="my_table">
	<h4>Number of report to generate in randomly selected categories:</h4>
	<?php print form::input('thismanyreports', $form['thismanyreports'], ' class="text title_2"'); ?>
	<br/><br/>
	<h4>By clicking on "Save Settings" you will generate the number of reports you typed in above.</h4>
	<h4 style="color:red;">WARNING: Double check how many rows you are putting in. It's only your fault if you try and insert fifty bajillion rows. THIS CANNOT BE UNDONE AUTOMAGICALLY!</h4>
</table>