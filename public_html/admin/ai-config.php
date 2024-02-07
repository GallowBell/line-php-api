<?php 

    require_once __DIR__ . "/../config.php";
    require_once __DIR__ . "/middleware/index.php";

    if($UserData[0]['access_level'] < 10){
        header('Location: /');
        exit;
    }

    /**
     * * $APP_URL + "/line" + $API_PRE_URL
     * @var string $API_URL
     */
    $API_URL = $APP_URL . $_ENV['API_PRE_URL'];

    /**
     * LINE userId
     * @var string $userId
     */
    $userId = $UserData[0]['userId'];

?>

<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>DataTable | LINE User Management</title>

    <meta name="description" content="" />

    <?php include_once __DIR__ . '/../require_page/header.php'; ?>

    <!-- Vendors CSS -->

    <!-- Page CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <?php include_once __DIR__ . '/../require_page/sub_header.php'; ?>

    <style>
      /* css sweet alert2 over modal */
      .swal2-container {
        z-index: 10000;
      }

      .pe-pointer {
        cursor: pointer;
      }

    </style>

  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <?php include_once __DIR__ . '/require_page/left_menu.php'; ?>

        <!-- Layout container -->
        <div class="layout-page">

          <?php include_once __DIR__ . '/require_page/navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">

            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Menu /</span> Sub Menu</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- content -->
                            </div><!-- / card-body -->
                        </div><!-- / card -->
                    </div><!-- / col-12 -->
                </div><!-- / row -->
            </div><!-- / Container-xxl -->
            <!-- / Content -->

            <?php include_once __DIR__ . '/../require_page/footer_html.php'; ?>

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <?php require_once __DIR__ . '/../require_page/footer.php'; ?>

    <!-- Page JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>  

    s

  </body>
</html>
