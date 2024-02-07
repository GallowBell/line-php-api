<?php 

if(!isset($APP_URL)){
    header('Location: 404');
}

?>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?php echo ASSET_URL('/vendor/libs/jquery/jquery.js'); ?>"></script>
    <script src="<?php echo ASSET_URL('/vendor/libs/popper/popper.js'); ?>"></script>
    <script src="<?php echo ASSET_URL('/vendor/js/bootstrap.js'); ?>"></script>
    <script src="<?php echo ASSET_URL('/vendor/libs/node-waves/node-waves.js'); ?>"></script>
    <script src="<?php echo ASSET_URL('/vendor/js/menu.js'); ?>"></script>
    <script src="<?php echo ASSET_URL('/js/function.js'); ?>"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?php echo ASSET_URL('/js/main.js'); ?>"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Cleave JS -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>