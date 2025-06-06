<?php

if($UserData[0]['access_level'] == 1000) {
    $access_level_Text = 'Super Admin';
}

if($UserData[0]['access_level'] == 100) {
    $access_level_Text = 'Admin';
}

if($UserData[0]['access_level'] == 10) {
    $access_level_Text = 'User';
}

if($UserData[0]['access_level'] == 1) {
    $access_level_Text = 'Guest';
}

$pictureUrl = $UserData[0]['pictureUrl']?$UserData[0]['pictureUrl']:ASSET_URL('/img/avatars/1.png');

$full_name = $UserData[0]['fname'] . ' ' . $UserData[0]['lname'];

$displayName = $UserData[0]['displayName'];

?>

<!-- Navbar -->
<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
    <i class="mdi mdi-menu mdi-24px"></i>
    </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            <i class="mdi mdi-magnify mdi-24px lh-0"></i>
            <input
            type="text"
            class="form-control border-0 shadow-none bg-body"
            placeholder="Search..."
            aria-label="Search..." />
        </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- Place this tag where you want the button to render. -->

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a
        class="nav-link dropdown-toggle hide-arrow p-0"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
                <img src="<?php echo $pictureUrl; ?>" alt class="w-px-40 h-auto rounded-circle" />
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
            <li>
                <a class="dropdown-item pb-2 mb-1" href="#">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2 pe-1">
                            <div class="avatar avatar-online">
                                <img src="<?php echo $pictureUrl; ?>" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?php echo $displayName; ?></h6>
                            <small class="text-muted"><?php echo $access_level_Text; ?></small>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <div class="dropdown-divider my-1"></div>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                    <i class="mdi mdi-account-outline me-1 mdi-20px"></i>
                    <span class="align-middle">My Profile</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                    <i class="mdi mdi-cog-outline me-1 mdi-20px"></i>
                    <span class="align-middle">Settings</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                    <span class="d-flex align-items-center align-middle">
                        <i class="flex-shrink-0 mdi mdi-credit-card-outline me-1 mdi-20px"></i>
                        <span class="flex-grow-1 align-middle ms-1">Billing</span>
                        <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                    </span>
                </a>
            </li>
            <li>
                <div class="dropdown-divider my-1"></div>
            </li>
            <li>
                <a class="dropdown-item" href="<?php echo $_ENV['REVOKE_PATH']; ?>">
                    <i class="mdi mdi-power me-1 mdi-20px"></i>
                    <span class="align-middle">Log Out</span>
                </a>
            </li>
        </ul>
    </li>
    <!--/ User -->
    </ul>
</div>
</nav>

<!-- / Navbar -->