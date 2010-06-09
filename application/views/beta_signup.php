<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Crowdmap</title>

<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript" src="beta-signup/js/jquery.hint.js"></script>
<script type="text/javascript" language="javascript" src="beta-signup/js/jquery.nyroModal-1.6.2.pack.js"></script>
<script type="text/javascript" language="javascript">
$(function(){
	$('a.toggle').toggle(function(){
		$(".bling").show()
		$(this).text("On");
	}, function(){
		$(".bling").hide();
		$(this).text("Off");
	});
		   
});
</script>
<script type="text/javascript" charset="utf-8">
	$(function(){ 
		// find all the input elements with title attributes
		$('input[title!=""]').hint();
	});
</script>

<?php
	if(isset($_GET['confirmed'])){
?>

<script type="text/javascript">
$(function() {
  $(window).load(function(e) {
    e.preventDefault();
    
    var content = '<div style="background-color:#FFFFFF;border:6px #2C2C2C solid;padding:20px;"><strong>Success!</strong><p>Your email address has been confirmed. We\'ll send on an invite soon.</p><p>Sincerely,</p><p>Team Ushahidi</p><p>P.S. Do stay in touch on <a href="http://www.facebook.com/pages/Ushahidi/116038145082895">Facebook</a> and <a href="http://twitter.com/Crowdmap">Twitter</a></div>';
    
    $.nyroModalManual({
      bgColor: '#2C2C2C',
      content: content,
      closeButton: '<a href="#" class="nyroModalClose" id="closeBut" title="close" style="float:right;background-color:#000000;"><img src="beta-signup/img/close.gif" alt="Close" style="border-color:#000000;" /></a>'

    });
    
    return false;
  });
});
</script>

<?php
	}
?>

<?php
	if(isset($_GET['signedup'])){
?>

<script type="text/javascript">
$(function() {
  $(window).load(function(e) {
    e.preventDefault();
    
    var content = '<div style="background-color:#FFFFFF;border:6px #2C2C2C solid;padding:20px;"><p><strong> We really appreciate your interest in Crowdmap!</strong></p> <p>Crowdmap takes the Ushahidi platform into the cloud.  A fully hosted, no-install-required option for crowdsourcing and visualizing information. We\'re almost ready to roll this out. You\'ll get an invite soon.</p> <p>Until then, stay tuned on <a href="http://twitter.com/Crowdmap">Twitter</a> and <a href="http://www.facebook.com/pages/Ushahidi/116038145082895">Facebook</a>.</p><p>Sincerely,</p> <p>Team Ushahidi</p></div>';
    
    $.nyroModalManual({
      bgColor: '#2C2C2C',
      content: content,
      closeButton: '<a href="#" class="nyroModalClose" id="closeBut" title="close" style="float:right;background-color:#000000;"><img src="beta-signup/img/close.gif" alt="Close" style="border-color:#000000;" /></a>'

    });
    
    return false;
  });
});
</script>

<?php
	}
?>

<style type="text/css">
    html { height:100%; margin:0; padding:0; vertical-align:baseline }
	body { background:#444546 url(beta-signup/img/bg_body.gif) repeat center top; color:#777; margin:0; padding:0; vertical-align:baseline; font-family:Verdana, Geneva, sans-serif; }

	#outer-header-bg { background:transparent url(beta-signup/img/bg_header.jpg) repeat-x center top;  }
	
	#content-wrap { background:transparent url(beta-signup/img/bg_header-center.jpg) no-repeat; width:860px; height:700px; margin:0 auto; padding:1px 0; position:relative; }
	
	a { color:#ac2505; outline:none; }
	a:hover { color:#d42e06; }
	
	h1, h2, ul li { overflow:hidden; text-indent:-23422px;margin:0; padding:0; }
	
	h1 { background:url(beta-signup/img/Crowdmap-Logo_beta.png) top center no-repeat; height:85px; margin:200px 0 0 0; }
	
	p.sign-up { text-align:center; margin:110px 0 0 0; position:relative; }
	p span { position:absolute; top:45px; left:298px; font-size:10px; }
	p.footer { font-size:10px; text-align:center; margin:25px 0 0 0; } 
	p.footer a { text-transform:uppercase; } 
	
	p.go { font-size:14px; text-align:center; margin:50px 0 0 0; } 
	p.go a { text-transform:uppercase; }
	
	label { display:none; }
	input.text { color:#606060;
	color:#606060; font-size:0.75em; height:27px;
	margin:0 5px 1px; padding:2px 8px;
	vertical-align:bottom; width:210px; 
	border:2px solid;
	}

	input.btn_submit  {
		background:url(beta-signup/img/btn_let-me-in.png) no-repeat scroll left top transparent;
		border:0 none; cursor:pointer; font-size:0;
		line-height:0; outline:medium none;
		overflow:hidden; text-indent:-2030px; height:38px; width:190px;
	}
	input.btn_submit:hover  { background:url(beta-signup/img/btn_let-me-in.png) no-repeat scroll left bottom transparent; }
	
	/*bling*/
	.bling { display:none; }
	#bling-aggregate { position:absolute; top:334px; left:-25px; }
	#bling-realtime { position:absolute; top:280px; right:33px;  }
	#bling-hotness { position:absolute; top:412px; right:84px; }
	
</style>

</head>

<body>
    <div id="outer-header-bg">
    	<div id="content-wrap">
        	<h1>Crowdmap</h1>
            <h2>Hosted crowdsourcing.</h2>
            <ul>
                <li>Gather information from cell phones, email and the web.</li>
                <li>Aggregate that information into a single collection.</li>
                <li>Visualize it on a map and timeline.</li> 
            </ul>
        
            <form action="http://ushahidi.createsend.com/t/y/s/kytjz/" method="post" id="subForm">
               <p class="sign-up">
                    <label for="email">Enter your email address</label>
                    <input  type="text" class="text" name="cm-kytjz-kytjz" id="kytjz-kytjz" value="" title="Enter your email address" />
                    <span>We won't spam you. Fullstop.</span>
                    <input  type="submit" class="btn_submit" value="Subscribe" />
                    
                </p>
            </form>
            
            <p class="go">Already signed up? Go to the site and get going, <a href="?go=1">here</a>.</p>

            <p class="footer">&copy; Copyright 2010 Ushahidi : All Rights Reserved : Special Features: <a href="#" class="toggle">off</a></p>
            
            <div class="bling">
            	<img id="bling-aggregate" src="beta-signup/img/bling_Agregate.png" />
                <img id="bling-hotness" src="beta-signup/img/bling_Hotness.png" />
                <img id="bling-realtime" src="beta-signup/img/bling_Real-Time.png" />
            </div>
        </div>	
    </div>
    
</body>
</html>
