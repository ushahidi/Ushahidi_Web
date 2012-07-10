<body>
	<div id="ushahidi_install_container">
	    <div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
	    <div id="ushahidi_login" class="clearfix">
	    <form method="POST" name="frm_install" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
		
		<?php if (isset($errors)): ?>
	   		<div class="feedback error"><a class="btn-close" href="#">x</a>
	       		<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
					<?php foreach ($errors as $error): ?>
						<li><?php print $error; ?></li>
					<?php endforeach; ?>
					</ul>
				</p>
			</div>
		<?php endif; ?>
		
		<?php if ($install_mode === 'basic'): ?>
	    <div class="feedback info"> 
	    	<p>Before you get started, you will need to make sure the following files and folders are writable by your webserver. This involves changing file permissions.</p>
	            <ul>
	                <li>application/config</li>
	                <li>application/cache</li>
	                <li>application/logs</li>
	                <li>media/uploads</li>
	                <li>.htaccess</li>
	            </ul> 

	        <p>Here are instructions for changing file permissions:</p>
	        <ul>
	            <li><a href="http://www.washington.edu/computing/unix/permissions.html" target="_blank">Unix/Linux</a></li>
	            <li><a href="http://support.microsoft.com/kb/308419" target="_blank">Windows</a></li>
	        </ul>
	    </div>

		<p>For the installation process, please have the following bits of information on hand.</p>	
		<div class="two-col tc-left">
	        <h3>Database <a href="http://wiki.ushahidi.com/doku.php?id=a_brief_word_on_databases" target="_blank">what's this?</a></h3>
	        <ol>	
	            <li>Database name</li>
	            <li>Database username</li>
	            <li>Database password</li>
	            <li>Database host</li>

	        </ol>
	    </div>
	    <div class="two-col tc-right">
	        <h3>General</h3>
	        <ol>	
	            <li>Site name &amp; tagline</li>
	            <li>Site Email Address</li>
	        </ol>
	    </div>
	    <div style="clear:both"></div>
		<p>
			<input type="submit" name="previous" class="button" value="&larr; Go back"/>&nbsp;&nbsp;
			<input type="submit" name="continue" value="Let's get started!" class="button"  /></p>
		</div>
		
		<?php elseif ($install_mode === 'advanced'): ?>
			<p>Before you get started, you will need to make sure the following files and folders are writable by your webserver. This involves changing file permissions.</p>
			<div class="two-col tc-left">
		        <h3>Database <a href="http://wiki.ushahidi.com/doku.php?id=a_brief_word_on_databases" target="_blank">what's this?</a></h3>
		        <ol>	
		            <li>Database name</li>
		            <li>Database username</li>
		            <li>Database password</li>
		            <li>Database host</li>
		        </ol>

		        <h3>General</h3>
		        <ol>	
		            <li>Site name &amp; tagline</li>
		            <li>Site Email Address</li>
		        </ol>
		    </div>
		    <div class="two-col tc-right last">
		    	<h3>Mail Server</h3>
		        <ol>
		            <li>Site alert email address</li>
		            <li>Mail Server Username</li>
		            <li>Mail Server Password</li>
		            <li>Mail Server Port</li>
		            <li>Mail Server Host</li>
		            <li>Mail Server Type</li>
		        </ol>
		        <h3>Map</h3>
		        <ol>	
		            <li>Map Provider</li>
		            <li>API Key</li>
		        </ol>
			</div>	

			
			<div class="actions clearfix">
				<div class="next"><input type="submit" name="continue" value="Continue &rarr;" class="button" /></div>
				<div class="prev"><input type="submit" name="previous" value="&larr; Previous" class="button" /></div>
			</div>
			</div>

		<?php endif; ?>
		<form>
	<div>
<body>
<html>