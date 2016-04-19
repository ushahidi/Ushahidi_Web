<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $page_title.$site_name; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo Kohana::config('core.site_protocol'); ?>://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel="stylesheet" type="text/css">
<?php echo $header_block; ?>
<?php
// Action::header_scripts - Additional Inline Scripts from Plugins
Event::run('ushahidi_action.header_scripts');
?>
<link rel="stylesheet" type="text/css" href="<?php print URL::base() ?>themes/simple-responsive/assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?php print URL::base() ?>themes/simple-responsive/assets/bootstrap/css/bootstrap-theme.min.css">
<script type="text/javascript" src="<?php print URL::base() ?>themes/simple-responsive/assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php print URL::base() ?>themes/simple-responsive/js/jquery.browser.js"></script>
</head>
<?php
  // Add a class to the body tag according to the page URI
  // we're on the home page
  if (count($uri_segments) == 0)
  {
    $body_class = "page-main";
  }
  // 1st tier pages
  elseif (count($uri_segments) == 1)
  {
    $body_class = "page-".$uri_segments[0];
  }
  // 2nd tier pages... ie "/reports/submit"
  elseif (count($uri_segments) >= 2)
  {
    $body_class = "page-".$uri_segments[0]."-".$uri_segments[1];
  }

?>

<body id="page" class="<?php echo $body_class; ?>">

<?php echo $header_nav; ?>

    <!-- header -->
    <div id="header">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <!-- logo -->
                    <?php if ($banner == NULL): ?>
                    <div id="logo">
                        <h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
                        <span><?php echo $site_tagline; ?></span>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo url::site();?>"><img src="<?php echo $banner; ?>" alt="<?php echo $site_name; ?>" /></a>
                    <?php endif; ?>
                    <!-- / logo -->
                </div>
                <div class="col-md-6 col-xs-12">
                    <!-- submit incident -->
                    <?php echo $submit_btn; ?>
                    <!-- / submit incident -->
                </div>
            </div>
        </div>
    </div>

        <?php
            // Action::main_sidebar - Add Items to the Entry Page Sidebar
            Event::run('ushahidi_action.main_sidebar');
        ?>

    </div>
    <!-- / header -->
     <!-- / header item for plugins -->
    <?php
        // Action::header_item - Additional items to be added by plugins
        Event::run('ushahidi_action.header_item');
    ?>

    <?php if(isset($site_message) AND $site_message != '') { ?>
        <div class="green-box">
            <h3><?php echo $site_message; ?></h3>
        </div>
    <?php } ?>

    <!-- mainmenu -->
    <nav id="myNavbar" class="navbar navbar-default navbar-inverse" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainmenu">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="mainmenu">
                <ul class="nav navbar-nav">
                    <?php nav::main_tabs($this_page); ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- / mainmenu -->
