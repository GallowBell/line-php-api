<?php 

if(!isset($APP_URL)){
    header('Location: 404');
}

?>
 
    <!-- Helpers -->
    <script src="<?php echo ASSET_URL('/vendor/js/helpers.js'); ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo ASSET_URL('/js/config.js'); ?>"></script>