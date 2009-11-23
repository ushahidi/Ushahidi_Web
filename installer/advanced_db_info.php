<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Database Connections / Ushahidi Web Installer</title>
<link href="../media/css/admin/login.css" rel="stylesheet" type="text/css" />
</head>
<script src="../media/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="../media/js/login.js" type="text/javascript" charset="utf-8"></script>

<body>
<div id="ushahidi_login_container" class="advanced">
    <div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
    <div id="ushahidi_login">
	<ol class="progress-meter clearfix">
		<li class="active"><span>Database</span></li>
		<li class=""><span>General</span></li>
		<li class=""><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="advanced-general-settings.html" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<li><strong>Oops!</strong> Ushahidi is trying to create and/or edit a file called "database.php" and is unable to do so at the moment. This is probably due to the fact that your permissions aren't set up properly for the <code>config</code> folder. Please change the permissions of that folder to allow write access (666).  More information on changing file permissions can be found at the following links: <a href="http://www.washington.edu/computing/unix/permissions.html">Unix/Linux</a>, <a href="http://support.microsoft.com/kb/308419">Windows.</a> </li>
                    	<li>Please make sure to enter the <strong>username</strong> of the database server.</li>
                        <li>Please enter the <strong>name</strong> of your database.</li>
                        
					</ul>
				</div>
                
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="base_path">Base Path</label></th>
                            <td><input type="text" value="ushahidi" size="25" id="base_path" name="base_path"/></td>
                            <td>The location on your server where you placed your Ushahidi files. We have automatically detected this value, please make sure that it is correct.</td>
                        </tr>
                        <!-- We don't support postgresql just yet
                        	<tr>
                            <th scope="row"><label for="host">Database Type</label></th>
                            <td>
                            	<select name="select_db_type">
                                        <option value="mysql">mysql</option>
                                        <option value="postgresql">postgresql</option>
                                </select>
                            </td>
                            <td>The type of database you want to use.</td>
                        </tr>-->
                        <tr>
                            <th scope="row"><label for="db_name">Database Name</label></th>
                            <td><input type="text" value="db_ushahidi" size="25" id="db_name" name="db_name"/></td>
                            <td>The name of the database you want to run Ushahidi in. </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="username">User Name</label></th>
                            <td><input type="text" value="username" size="25" id="username" name="username"/></td>
                            <td>Your database username</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="pwd">Password</label></th>
                            <td><input type="text" value="password" size="25" id="password" name="password"/></td>
                            <td>Your database password.</td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="host">Database Host</label></th>
                            <td><input type="text" value="localhost" size="25" id="host" name="host"/></td>
                            <td>If you are running Ushahidi on your own computer, this will more than likely be "localhost". If you are running Ushahidi from a web server, you'll get your host information from your web hosting provider.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="table_prefix">Table Prefix</label></th>
                            <td><input type="text" size="25" value="ush_" id="table_prefix" name="table_prefix"/></td>
                            <td>Normally you won't change the table prefix.  However, If you want to run multiple Ushahidi installations from a single database you can do that by changing the prefix here.</td>
                        </tr>
                        <input type="hidden" name="connection" />
                        <input type="hidden" name="permission" />
                        <input type="hidden" name="load_db_tpl" />
                        
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><!--<input type="button" class="button" value="Continue &rarr;" value="submit"  />-->
                      						 <a class="button" href="advanced-general-settings.html">Continue &rarr;</a></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
  </div>

</div>
</body>
</html>
