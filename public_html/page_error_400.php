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

    <title>Error - Pages</title>

    <meta name="description" content="" />

    <?php require_once __DIR__ . '/require_page/header.php'; ?>
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/css/pages/page-misc.css'); ?>" />
    <?php require_once __DIR__ . '/require_page/sub_header.php'; ?>
  </head>

  <body>
    <!-- Content -->

    <!-- Error -->
    <div class="misc-wrapper">
      <h1 class="mb-2 mx-2" style="font-size: 6rem">404</h1>
      <h4 class="mb-2">Page Not Found ⚠️</h4>
      <p class="mb-4 mx-2">we couldn't find the page you are looking for</p>
      <div class="d-flex justify-content-center mt-5">
        <img
          src="<?php echo ASSET_URL('/img/illustrations/tree.png'); ?>"
          alt="misc-tree"
          class="img-fluid misc-object d-none d-lg-inline-block"
          width="80" />
        <img
          src="<?php echo ASSET_URL('/img/illustrations/misc-mask-light.png'); ?>"
          alt="misc-error"
          class="scaleX-n1-rtl misc-bg d-none d-lg-inline-block"
          data-app-light-img="illustrations/misc-mask-light.png"
          data-app-dark-img="illustrations/misc-mask-dark.png" />
        <div class="d-flex flex-column align-items-center">
          <img
            src="<?php echo ASSET_URL('/img/illustrations/404.png'); ?>"
            alt="misc-error"
            class="misc-model img-fluid z-1"
            width="780" />
          <div>
            <a href="<?php echo $URL_LOGIN_WEB; ?>" class="btn btn-primary text-center my-4">Back to home</a>
          </div>
        </div>
      </div>
    </div>
    <!-- /Error -->

    <!-- / Content -->

    <?php require_once __DIR__ . '/require_page/footer.php'; ?>
  </body>
</html>
