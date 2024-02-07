<?php 

require_once __DIR__ ."/config.php";

$APP_URL = $_ENV['APP_URL'];
$URL_LOGIN_WEB = $APP_URL.$_ENV['URL_LOGIN_WEB'];

?>
<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Under Maintenance</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo ASSET_URL('/img/favicon/favicon.ico'); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <?php require_once __DIR__ . '/require_page/header.php'; ?>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/css/pages/page-misc.css'); ?>" />
    
    <?php require_once __DIR__ . '/require_page/sub_header.php'; ?>

  </head>

  <body>
    <!-- Content -->

    <!--Under Maintenance -->
    <div class="misc-wrapper">
      <h3 class="mb-2 mx-2">Under Maintenance! 🚧</h3>
      <p class="mb-4 mx-2">Sorry for the inconvenience but we're performing some maintenance at the moment</p>
      <div class="d-flex justify-content-center mt-5">
        <img
          src="<?php echo ASSET_URL('/img/illustrations/tree-3.png'); ?>"
          alt="misc-tree"
          class="img-fluid misc-object d-none d-lg-inline-block"
          width="150" />
        <img
          src="<?php echo ASSET_URL('/img/illustrations/misc-mask-light.png'); ?>"
          alt="misc-error"
          class="scaleX-n1-rtl misc-bg d-none d-lg-inline-block"
          data-app-light-img="illustrations/misc-mask-light.png"
          data-app-dark-img="illustrations/misc-mask-dark.png" />
        <div class="d-flex flex-column align-items-center">
          <img
            src="<?php echo ASSET_URL('/img/illustrations/misc-under-maintenance.png'); ?>"
            alt="misc-error"
            class="img-fluid z-1"
            width="780" />
          <div>
          </div>
        </div>
      </div>
    </div>
    <!-- /Under Maintenance -->

    <!-- / Content -->

    <?php require_once __DIR__ . '/require_page/footer.php'; ?>
  </body>
</html>
