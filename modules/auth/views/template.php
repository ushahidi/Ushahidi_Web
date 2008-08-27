<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

	<head>
		<title><?php echo $title;?></title>
		<style type="text/css">
			input
			{
				display:block;
				margin-bottom:1em;
			}
			input[type="checkbox"]
			{
				display:inline;
				margin:0;
			}
			input[type="submit"]
			{
				margin:1em 0;
			}
			fieldset
			{
				border:0;
				padding:1em 0;
			}
			legend
			{
				font-size:18px;
			}
			.error
			{
				color:#f00;
			}
            #openid_url
            {
            	background: #FFFFFF url(<?php echo '"'.url::base().'assets/images/openid-icon-small.gif"';?>) no-repeat scroll 0pt 50%;
            	padding-left: 18px;
            }
		</style>
	</head>


	<body>

<?php echo $content;?>

	</body>


</html>