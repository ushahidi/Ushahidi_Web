<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $page_title.$site_name; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta property="og:site_name" content="AyudaEcuador - Mapa">
<meta property="og:title" content="Reporta tu emergencia, daño o pedido de ayuda">
<meta property="og:description" content="Una plataforma para el mapeo de los informes sobre los daños ocurridos por el terremoto que afectó a las costas ecuatorianas el 16 de abril del 2016.">
<meta property="og:image" content="http://desastre.ec/img/hug.jpg">
<meta property="og:rich_attachment" content="true">

<link href="<?php echo Kohana::config('core.site_protocol'); ?>://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel="stylesheet" type="text/css">
<?php echo $header_block; ?>
<?php
// Action::header_scripts - Additional Inline Scripts from Plugins
Event::run('ushahidi_action.header_scripts');
?>
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

<body id="page" class="<?php echo $body_class; ?>" style="margin-top: 50px;">

<!-- <?php echo $header_nav; ?> -->
  <nav class="navbar navbar-default navbar-fixed-top top-nav-collapse" role="navigation">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="http://www.desastre.ec/" style="font-weight: bold;">Ayuda Ecuador</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a class="page-scroll" href="#page-top"></a>
                    </li>
                    <?php nav::main_tabs($this_page); ?>
                    <li style="float: right;">
                        <a href="/login">Ingresar</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
    <!-- ABAJO EL VIEJO -->

    <!-- header -->
    <div id="header">
      <div class="container vcenter">
        <div class="cover">
        </div>
        <div class="row center-text">
          <div class="col-lg-12 big_title">
            <h1 class="big_white">AyudaEcuador</h1>
          </div>
        </div>
      </div>
    </div>
    <section id="quienessomos" class="quienessomos-section">
      <div class="container">
      <div class="row">

          <div class="col-sm-12 col-xs-12" style="margin-top: 10px;">
              <!-- submit incident -->
              <?php echo $submit_btn; ?>
              <!-- / submit incident -->
          </div>
        <div class="col-lg-12">
          <p class="lead">
            <?php if(isset($site_message) AND $site_message != '') { ?>
                <?php echo $site_message; ?>
            <?php } ?>
          </p>
        </div>
      </div>

        </div>
    </section>

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
