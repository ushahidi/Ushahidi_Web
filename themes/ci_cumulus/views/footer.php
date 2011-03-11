
<iframe src="https://spreadsheets.google.com/embeddedform?formkey=dE9CbGFHTURIS3RjLWd2aGJlX0Q2aHc6MQ" width="952" height="625" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" id="feedbackform">Loading...</iframe>

<p id="footer"><small>Theme hacked away by <a href="http://ushahidi.com">Ushahidi</a> using the Wordpress theme, Cumulus by <a href="http://empirethemes.com">Empire Themes</a>.</small></p>
	<?php echo $ushahidi_stats; ?>
	<?php echo $google_analytics; ?>
	
	<!-- Task Scheduler -->
	<img src="<?php echo url::base(); ?>media/img/spacer.gif" alt="" height="1" width="1" border="0" onload="runScheduler(this)" />

<?php
	
	// Turn on picbox
	echo html::script('media/js/picbox', true);
	echo html::stylesheet('media/css/picbox/picbox');
	
	// Action::main_footer - Add items before the </body> tag
	Event::run('ushahidi_action.main_footer');
?>

</body>
</html>
