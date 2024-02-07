<?php 

if(!isset($APP_URL)){
    header('Location: 404');
}

?>

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?php echo ASSET_URL('/img/favicon/favicon.ico'); ?>" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
    rel="stylesheet" />

<link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/fonts/materialdesignicons.css'); ?>" />

<!-- Menu waves for no-customizer fix -->
<link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/node-waves/node-waves.css'); ?>" />

<!-- Core CSS -->
<link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/css/core.css" class="template-customizer-core-css'); ?>" />
<link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/css/theme-default.css" class="template-customizer-theme-css'); ?>" />
<link rel="stylesheet" href="<?php echo ASSET_URL('/css/demo.css'); ?>" />


<!-- Vendors CSS -->
<link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'); ?>" />
