<?php 

  require_once __DIR__ . "/../config.php";
  require_once __DIR__ . "/middleware/index.php";

  $APP_URL = $_ENV['APP_URL'];

  $API_URL = $APP_URL . $_ENV['API_PRE_URL'];

  //$API_GET_DEMOGRAPHICS = $API_URL ."/get-demographics";

  /**
   * @var bool $Dummy_DATA Using dummy data
   * * true for use dummy data
   * * false for use real data
   */
  $Dummy_DATA = true;

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

    <title>Home | LINE Admin Dashboard</title>

    <meta name="description" content="" />

    <?php require_once __DIR__ . '/../require_page/header.php'; ?>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'); ?>" />
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/apex-charts/apex-charts.css'); ?>" />

    <!-- Page CSS -->
    <?php require_once __DIR__ . '/../require_page/sub_header.php'; ?>
    
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <?php require_once __DIR__ . '/require_page/left_menu.php'; ?>

        <!-- Layout container -->
        <div class="layout-page">
          
          <?php require_once __DIR__ . '/require_page/navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row gy-4">
                <!-- Info Bot card -->
                <div class="col-md-12 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title mb-1" >Bot Info 🎉</h4>
                      <p class="pb-0">ข้อมูลบอท</p>
                      <h4 class="text-primary mb-1 d-flex text-truncate col-sm-9 col-md-6" >
                        <i class="mdi mdi-account-circle mdi-24px"></i>
                        <span class="ms-1 fw-normal " id="bot-displayName">
                          <span class="placeholder-glow">
                            <span class="placeholder col-lg-6 bg-secondary"></span>
                          </span>
                        </span>
                      </h4>
                    </div>
                    <img
                      src="<?php echo ASSET_URL('/img/icons/misc/triangle-light.png'); ?>"
                      class="scaleX-n1-rtl position-absolute bottom-0 end-0"
                      width="117"
                      alt="triangle background"
                      data-app-light-img="icons/misc/triangle-light.png"
                      data-app-dark-img="icons/misc/triangle-dark.png" />
                    <img
                      id="bot-pictureUrl"
                      src="<?php echo ASSET_URL('/img/avatars/1.png'); ?>"
                      class="scaleX-n1-rtl position-absolute bottom-0 end-0 m-4 p-1 border border-primary rounded"
                      width="100"
                      alt="Bot Profile" />
                  </div>
                </div>
                <!--/ Info BOT card -->
                <!-- Follower Bot -->
                <div class="col-lg-8">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <h5 class="mb-2 pb-1 fw-normal" id="bot-basicId">
                            <span class="placeholder-glow">
                              <span class="placeholder col-lg-7 bg-secondary"></span>
                            </span>
                          </h5>
                        </div>
                        <div class="col-6">
                          <div class="mb-2 pb-1" id="bot-status">
                            <span class="placeholder-glow">
                              <span class="placeholder col-lg-7 bg-secondary"></span>
                            </span>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="mb-2 pb-1" id="bot-followers">
                            <span class="placeholder-glow">
                              <span class="placeholder col-lg-7 bg-secondary"></span>
                            </span>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="mb-2" id="bot-targetedReaches">
                            <span class="placeholder-glow">
                              <span class="placeholder col-lg-7 bg-secondary"></span>
                            </span>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="mb-2" id="bot-blocks">
                            <span class="placeholder-glow">
                              <span class="placeholder col-lg-7 bg-secondary"></span>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /Follower Bot -->
                <!-- AppTypes -->
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">ประเภท OS ของผู้ใช้งาน LINE</h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="transactionID"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="AppType.update()">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                          </div>
                        </div>
                      </div>
                      <!-- <p class="mt-3"><span class="fw-medium">ประเภท OS ของผู้ใช้งาน LINE</span> </p> -->
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <div id="chart_AppType" style="height:350px"></div>
                        </div>
                      </div>
                      <div class="row g-3 d-flex justify-content-between mt-3">
                        <div class="col-sm-12 col-md-3 col-4">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-primary rounded shadow">
                                <i class="mdi mdi-apple-ios mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">iOS</div>
                              <h5 class="mb-0" id="appType_ios"></h5>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-4">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-success rounded shadow">
                                <i class="mdi mdi-android mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                            <div class="small mb-1">Android</div>
                              <h5 class="mb-0" id="appType_android"></h5>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-4">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-info rounded shadow">
                                <i class="mdi mdi-monitor-cellphone mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">Others</div>
                              <h5 class="mb-0" id="appType_others"></h5>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ AppTypes -->

                <!-- Genders Chart -->
                <div class="col-lg-6">
                  <div class="card h-100">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                        <h5 class="mb-1">สัดส่วนเพศของผู้ใช้งาน</h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="weeklyOverviewDropdown"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            <a class="dropdown-item" href="javascript:void(0);">Update</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div id="chart_genders" style="height:350px">

                      </div>
                      <div class="mt-1 mt-md-3">
                        <div class="d-flex justify-content-between align-items-center gap-3">

                          <div class="col-sm-12 col-md-3 col-4">
                            <div class="d-flex align-items-center">
                              <div class="avatar">
                                <div class="avatar-initial bg-primary rounded shadow">
                                  <i class="mdi mdi-human-male mdi-24px"></i>
                                </div>
                              </div>
                              <div class="ms-3">
                              <div class="small mb-1">Male</div>
                                <h5 class="mb-0" id="gender_male"></h5>
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-12 col-md-3 col-4">
                            <div class="d-flex align-items-center">
                              <div class="avatar">
                                <div class="avatar-initial bg-danger rounded shadow">
                                  <i class="mdi mdi-human-female mdi-24px"></i>
                                </div>
                              </div>
                              <div class="ms-3">
                              <div class="small mb-1">Female</div>
                                <h5 class="mb-0" id="gender_female"></h5>
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-12 col-md-3 col-4">
                            <div class="d-flex align-items-center">
                              <div class="avatar">
                                <div class="avatar-initial bg-secondary rounded shadow">
                                  <i class="mdi mdi-account-question mdi-24px"></i>
                                </div>
                              </div>
                              <div class="ms-3">
                              <div class="small mb-1">Unknown</div>
                                <h5 class="mb-0" id="gender_unknown"></h5>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Genders Chart -->               

                <!-- Ages chart -->
                <div class="col-xl-6 col-md-6">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">ข้อมูลอายุ</h5>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="totalEarnings"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false">
                          <i class="mdi mdi-dots-vertical mdi-24px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalEarnings">
                          <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <div id="chart_age" style="height:350px">
                          
                          </div>
                        </div>
                      </div>
                    </div> 
                  </div>
                </div>
                <!--/ Ages chart -->

                <!-- subscriptionPeriods chart -->
                <div class="col-xl-6 col-md-6">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">ระยะเวลาในการติดตาม</h5>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="totalEarnings"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false">
                          <i class="mdi mdi-dots-vertical mdi-24px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalEarnings">
                          <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <div id="chart_subscriptionPeriods" style="height:350px">
                          
                          </div>
                        </div>
                      </div>
                    </div> 
                  </div>
                </div>
                <!--/ subscriptionPeriods chart -->

                <!-- Areas Chart -->
                <div class="col-lg-12">
                  <div class="card h-100">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                        <h5 class="mb-1">ข้อมูลพื้นที่ของผู้ใช้งาน</h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="weeklyOverviewDropdown"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            <a class="dropdown-item" href="javascript:void(0);">Update</a>
                          </div>
                        </div>
                       </div>
                    </div>
                    <div class="card-body">
                      <div id="chart_areas" style="height:350px">

                      </div>
                      
                    </div>
                  </div>
                </div>
                <!--/ Areas Chart -->

                <!-- LINE BOT Response Stats Chart -->
                <div class="col-lg-12">
                  <div class="card h-100">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                        <h5 class="mb-1">
                          ข้อมูลบอทแสดง 10 ลำดับมากสุดที่มีการตอบ ภายในเดือนนี้
                        </h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="bot-stats-response"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="bot-stats-response">
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            <a class="dropdown-item" href="javascript:void(0);">Update</a>
                          </div>
                        </div>
                       </div>
                    </div>
                    <div class="card-body">
                      <div id="chart_bot_stats" style="height:350px">

                      </div>
                      
                    </div>
                  </div>
                </div>
                <!--/ LINE BOT Response Stats Chart -->

                <!-- LINE BOT Caption Stats Chart -->
                <div class="col-lg-12">
                  <div class="card h-100">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                        <h5 class="mb-1">
                          ข้อมูลบอทแสดง 10 ลำดับมากสุดของ Caption ภายในเดือนนี้
                        </h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="bot-stats-caption"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="bot-stats-caption">
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            <a class="dropdown-item" href="javascript:void(0);">Update</a>
                          </div>
                        </div>
                       </div>
                    </div>
                    <div class="card-body">
                      <div id="chart_bot_caption_stats" style="height:350px">

                      </div>
                      
                    </div>
                  </div>
                </div>
                <!--/ LINE BOT Caption Stats Chart -->
                <?php 
                /*
                <!-- Four Cards -->
                <div class="col-xl-4 col-md-6">
                  <div class="row gy-4">
                    <!-- Total Profit line chart -->
                    <div class="col-sm-6">
                      <div class="card h-100">
                        <div class="card-header pb-0">
                          <h4 class="mb-0">$86.4k</h4>
                        </div>
                        <div class="card-body">
                          <div id="totalProfitLineChart" class="mb-3"></div>
                          <h6 class="text-center mb-0">Total Profit</h6>
                        </div>
                      </div>
                    </div>
                    <!--/ Total Profit line chart -->
                    <!-- Total Profit Weekly Project -->
                    <div class="col-sm-6">
                      <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                          <div class="avatar">
                            <div class="avatar-initial bg-secondary rounded-circle shadow">
                              <i class="mdi mdi-poll mdi-24px"></i>
                            </div>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="totalProfitID"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalProfitID">
                              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                              <a class="dropdown-item" href="javascript:void(0);">Share</a>
                              <a class="dropdown-item" href="javascript:void(0);">Update</a>
                            </div>
                          </div>
                        </div>
                        <div class="card-body mt-mg-1">
                          <h6 class="mb-2">Total Profit</h6>
                          <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                            <h4 class="mb-0 me-2">$25.6k</h4>
                            <small class="text-success mt-1">+42%</small>
                          </div>
                          <small>Weekly Project</small>
                        </div>
                      </div>
                    </div>
                    <!--/ Total Profit Weekly Project -->
                    <!-- New Yearly Project -->
                    <div class="col-sm-6">
                      <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                          <div class="avatar">
                            <div class="avatar-initial bg-primary rounded-circle shadow-sm">
                              <i class="mdi mdi-wallet-travel mdi-24px"></i>
                            </div>
                          </div>
                          <div class="dropdown">
                            <button
                              class="btn p-0"
                              type="button"
                              id="newProjectID"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="newProjectID">
                              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                              <a class="dropdown-item" href="javascript:void(0);">Share</a>
                              <a class="dropdown-item" href="javascript:void(0);">Update</a>
                            </div>
                          </div>
                        </div>
                        <div class="card-body mt-mg-1">
                          <h6 class="mb-2">New Project</h6>
                          <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                            <h4 class="mb-0 me-2">862</h4>
                            <small class="text-danger mt-1">-18%</small>
                          </div>
                          <small>Yearly Project</small>
                        </div>
                      </div>
                    </div>
                    <!--/ New Yearly Project -->
                    <!-- Sessions chart -->
                    <div class="col-sm-6">
                      <div class="card h-100">
                        <div class="card-header pb-0">
                          <h4 class="mb-0">2,856</h4>
                        </div>
                        <div class="card-body">
                          <div id="sessionsColumnChart" class="mb-3"></div>
                          <h6 class="text-center mb-0">Sessions</h6>
                        </div>
                      </div>
                    </div>
                    <!--/ Sessions chart -->
                  </div>
                </div>
                <!--/ Total Earning -->

                <!-- Sales by Countries -->
                <div class="col-xl-4 col-md-6">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">Sales by Countries</h5>
                      <div class="dropdown">
                        <button
                          class="btn p-0"
                          type="button"
                          id="saleStatus"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false">
                          <i class="mdi mdi-dots-vertical mdi-24px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="saleStatus">
                          <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                          <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3">
                            <div class="avatar-initial bg-label-success rounded-circle">US</div>
                          </div>
                          <div>
                            <div class="d-flex align-items-center gap-1">
                              <h6 class="mb-0">$8,656k</h6>
                              <i class="mdi mdi-chevron-up mdi-24px text-success"></i>
                              <small class="text-success">25.8%</small>
                            </div>
                            <small>United states of america</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <h6 class="mb-0">894k</h6>
                          <small>Sales</small>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3">
                            <span class="avatar-initial bg-label-danger rounded-circle">UK</span>
                          </div>
                          <div>
                            <div class="d-flex align-items-center gap-1">
                              <h6 class="mb-0">$2,415k</h6>
                              <i class="mdi mdi-chevron-down mdi-24px text-danger"></i>
                              <small class="text-danger">6.2%</small>
                            </div>
                            <small>United Kingdom</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <h6 class="mb-0">645k</h6>
                          <small>Sales</small>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3">
                            <span class="avatar-initial bg-label-warning rounded-circle">IN</span>
                          </div>
                          <div>
                            <div class="d-flex align-items-center gap-1">
                              <h6 class="mb-0">865k</h6>
                              <i class="mdi mdi-chevron-up mdi-24px text-success"></i>
                              <small class="text-success"> 12.4%</small>
                            </div>
                            <small>India</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <h6 class="mb-0">148k</h6>
                          <small>Sales</small>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3">
                            <span class="avatar-initial bg-label-secondary rounded-circle">JA</span>
                          </div>
                          <div>
                            <div class="d-flex align-items-center gap-1">
                              <h6 class="mb-0">$745k</h6>
                              <i class="mdi mdi-chevron-down mdi-24px text-danger"></i>
                              <small class="text-danger">11.9%</small>
                            </div>
                            <small>Japan</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <h6 class="mb-0">86k</h6>
                          <small>Sales</small>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3">
                            <span class="avatar-initial bg-label-danger rounded-circle">KO</span>
                          </div>
                          <div>
                            <div class="d-flex align-items-center gap-1">
                              <h6 class="mb-0">$45k</h6>
                              <i class="mdi mdi-chevron-up mdi-24px text-success"></i>
                              <small class="text-success">16.2%</small>
                            </div>
                            <small>Korea</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <h6 class="mb-0">42k</h6>
                          <small>Sales</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Sales by Countries -->

                <!-- Deposit / Withdraw -->
                <div class="col-xl-8">
                  <div class="card h-100">
                    <div class="card-body row g-2">
                      <div class="col-12 col-md-6 card-separator pe-0 pe-md-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                          <h5 class="m-0 me-2">Deposit</h5>
                          <a class="fw-medium" href="javascript:void(0);">View all</a>
                        </div>
                        <div class="pt-2">
                          <ul class="p-0 m-0">
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/gumroad.png"
                                  class="img-fluid"
                                  alt="gumroad"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Gumroad Account</h6>
                                  <small>Sell UI Kit</small>
                                </div>
                                <h6 class="text-success mb-0">+$4,650</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/mastercard-2.png"
                                  class="img-fluid"
                                  alt="mastercard"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Mastercard</h6>
                                  <small>Wallet deposit</small>
                                </div>
                                <h6 class="text-success mb-0">+$92,705</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/stripes.png"
                                  class="img-fluid"
                                  alt="stripes"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Stripe Account</h6>
                                  <small>iOS Application</small>
                                </div>
                                <h6 class="text-success mb-0">+$957</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/american-bank.png"
                                  class="img-fluid"
                                  alt="american"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">American Bank</h6>
                                  <small>Bank Transfer</small>
                                </div>
                                <h6 class="text-success mb-0">+$6,837</h6>
                              </div>
                            </li>
                            <li class="d-flex align-items-center">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/citi.png"
                                  class="img-fluid"
                                  alt="citi"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Bank Account</h6>
                                  <small>Wallet deposit</small>
                                </div>
                                <h6 class="text-success mb-0">+$446</h6>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 ps-0 ps-md-3 mt-3 mt-md-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                          <h5 class="m-0 me-2">Withdraw</h5>
                          <a class="fw-medium" href="javascript:void(0);">View all</a>
                        </div>
                        <div class="pt-2">
                          <ul class="p-0 m-0">
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/brands/google.png"
                                  class="img-fluid"
                                  alt="google"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Google Adsense</h6>
                                  <small>Paypal deposit</small>
                                </div>
                                <h6 class="text-danger mb-0">-$145</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/brands/github.png"
                                  class="img-fluid"
                                  alt="github"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Github Enterprise</h6>
                                  <small>Security &amp; compliance</small>
                                </div>
                                <h6 class="text-danger mb-0">-$1870</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/brands/slack.png"
                                  class="img-fluid"
                                  alt="slack"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Upgrade Slack Plan</h6>
                                  <small>Debit card deposit</small>
                                </div>
                                <h6 class="text-danger mb-0">$450</h6>
                              </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center pb-2">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/payments/digital-ocean.png"
                                  class="img-fluid"
                                  alt="digital"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">Digital Ocean</h6>
                                  <small>Cloud Hosting</small>
                                </div>
                                <h6 class="text-danger mb-0">-$540</h6>
                              </div>
                            </li>
                            <li class="d-flex align-items-center">
                              <div class="flex-shrink-0 me-3">
                                <img
                                  src="../assets/img/icons/brands/aws.png"
                                  class="img-fluid"
                                  alt="aws"
                                  height="30"
                                  width="30" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                  <h6 class="mb-0">AWS Account</h6>
                                  <small>Choosing a Cloud Platform</small>
                                </div>
                                <h6 class="text-danger mb-0">-$21</h6>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Deposit / Withdraw -->

                <!-- Data Tables -->
                <div class="col-12">
                  <div class="card">
                    <div class="table-responsive">
                      <table class="table">
                        <thead class="table-light">
                          <tr>
                            <th class="text-truncate">User</th>
                            <th class="text-truncate">Email</th>
                            <th class="text-truncate">Role</th>
                            <th class="text-truncate">Age</th>
                            <th class="text-truncate">Salary</th>
                            <th class="text-truncate">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Jordan Stevenson</h6>
                                  <small class="text-truncate">@amiccoo</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">susanna.Lind57@gmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-laptop mdi-24px text-danger me-1"></i> Admin
                            </td>
                            <td class="text-truncate">24</td>
                            <td class="text-truncate">34500$</td>
                            <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/3.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Benedetto Rossiter</h6>
                                  <small class="text-truncate">@brossiter15</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">estelle.Bailey10@gmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-pencil-outline text-info mdi-24px me-1"></i> Editor
                            </td>
                            <td class="text-truncate">29</td>
                            <td class="text-truncate">64500$</td>
                            <td><span class="badge bg-label-success rounded-pill">Active</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/2.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Bentlee Emblin</h6>
                                  <small class="text-truncate">@bemblinf</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">milo86@hotmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-cog-outline text-warning mdi-24px me-1"></i> Author
                            </td>
                            <td class="text-truncate">44</td>
                            <td class="text-truncate">94500$</td>
                            <td><span class="badge bg-label-success rounded-pill">Active</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Bertha Biner</h6>
                                  <small class="text-truncate">@bbinerh</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">lonnie35@hotmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-pencil-outline text-info mdi-24px me-1"></i> Editor
                            </td>
                            <td class="text-truncate">19</td>
                            <td class="text-truncate">4500$</td>
                            <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Beverlie Krabbe</h6>
                                  <small class="text-truncate">@bkrabbe1d</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">ahmad_Collins@yahoo.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-chart-donut mdi-24px text-success me-1"></i> Maintainer
                            </td>
                            <td class="text-truncate">22</td>
                            <td class="text-truncate">10500$</td>
                            <td><span class="badge bg-label-success rounded-pill">Active</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/7.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Bradan Rosebotham</h6>
                                  <small class="text-truncate">@brosebothamz</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">tillman.Gleason68@hotmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-pencil-outline text-info mdi-24px me-1"></i> Editor
                            </td>
                            <td class="text-truncate">50</td>
                            <td class="text-truncate">99500$</td>
                            <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Bree Kilday</h6>
                                  <small class="text-truncate">@bkildayr</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">otho21@gmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-account-outline mdi-24px text-primary me-1"></i> Subscriber
                            </td>
                            <td class="text-truncate">23</td>
                            <td class="text-truncate">23500$</td>
                            <td><span class="badge bg-label-success rounded-pill">Active</span></td>
                          </tr>
                          <tr class="border-transparent">
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                  <img src="../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div>
                                  <h6 class="mb-0 text-truncate">Breena Gallemore</h6>
                                  <small class="text-truncate">@bgallemore6</small>
                                </div>
                              </div>
                            </td>
                            <td class="text-truncate">florencio.Little@hotmail.com</td>
                            <td class="text-truncate">
                              <i class="mdi mdi-account-outline mdi-24px text-primary me-1"></i> Subscriber
                            </td>
                            <td class="text-truncate">33</td>
                            <td class="text-truncate">20500$</td>
                            <td><span class="badge bg-label-secondary rounded-pill">Inactive</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!--/ Data Tables -->
                */
                ?>
              </div>
            </div>
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

    <!-- Vendors JS -->
    <script src="<?php echo ASSET_URL('/vendor/libs/apex-charts/apexcharts.js'); ?>"></script>

    <!-- Page JS -->
    <!-- <script src="<?php echo ASSET_URL('/js/dashboards-analytics.js'); ?>"></script> -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script>

      const json_example = {
          "available": true,
          "genders": [
              {
                  "gender": "unknown",
                  "percentage": 37.6
              },
              {
                  "gender": "male",
                  "percentage": 31.8
              },
              {
                  "gender": "female",
                  "percentage": 30.6
              }
          ],
          "ages": [
           /*  {
              "age": "from0to14",
              "percentage": 10
            },
            {
              "age": "from15to19",
              "percentage": 10
            }, */
             //thailand not support from 0 to 19
            {
              "age": "from20to24",
              "percentage": 12.5
            },
            {
              "age": "from25to29",
              "percentage": 12.5
            },
            {
              "age": "from30to34",
              "percentage": 12.5
            },
            {
              "age": "from35to39",
              "percentage": 12.5
            },
            {
              "age": "from40to44",
              "percentage": 12.5
            },
            {
              "age": "from45to49",
              "percentage": 12.5
            },
            {
              "age": "from50",
              "percentage": 12.5
            },
            {
              "age": "unknown",
              "percentage": 12.5
            },
          ],
          "areas": [
            {
              "area": "Bangkok",
              "percentage": 11.11
            },
            {
              "area": "Pattaya",
              "percentage": 11.11
            },
            {
              "area": "Northern",
              "percentage": 11.11
            },
            {
              "area": "Central",
              "percentage": 11.11
            },
            {
              "area": "Southern",
              "percentage": 11.11
            },
            {
              "area": "Eastern",
              "percentage": 11.11
            },
            {
              "area": "NorthEastern",
              "percentage": 11.11
            },
            {
              "area": "Western",
              "percentage": 11.11
            },
            {
              "area": "unknown",
              "percentage": 11.11
            },
          ],
          "appTypes": [
              {
                  "appType": "ios",
                  "percentage": 62.4
              },
              {
                  "appType": "android",
                  "percentage": 27.7
              },
              {
                  "appType": "others",
                  "percentage": 9.9
              }
          ],
          "subscriptionPeriods": [
              {
                  "subscriptionPeriod": "over365days",
                  "percentage": 96.4
              },
              {
                  "subscriptionPeriod": "within365days",
                  "percentage": 1.9
              },
              {
                  "subscriptionPeriod": "within180days",
                  "percentage": 1.2
              },
              {
                  "subscriptionPeriod": "within90days",
                  "percentage": 0.5
              },
              {
                  "subscriptionPeriod": "within30days",
                  "percentage": 0.1
              },
              {
                  "subscriptionPeriod": "within7days",
                  "percentage": 0
              }
          ]
      };

      let Chart_AppTypes,
          bot_info,
          DemoGraphics,
          LINE_BOT_Stats = {
            TotalByResponse: false,
            TotalByCaption: false,
          };

      //function get innerhtml and print
      document.addEventListener('DOMContentLoaded', (event) => {
        
        let number_follower = getNumberFollwer();
        DemoGraphics = getDemoGraphics();

        getLINEBotStats();
        initBotInfo();
        initAppType();
        initGenders();
        initAges();
        initsubscriptionPeriods();
        initAreas();

        let interval = setInterval(() => {

          console.log(LINE_BOT_Stats)

          if(!LINE_BOT_Stats.TotalByCaption && !LINE_BOT_Stats.TotalByResponse){
            return;
          }

          DrawChartLINEBotStats({
            id: '#chart_bot_stats',
            data: LINE_BOT_Stats.TotalByResponse.data,
            date_start: LINE_BOT_Stats.TotalByResponse.date_start,
            date_end: LINE_BOT_Stats.TotalByResponse.date_end,
          });

          DrawChartLINEBotStats({
            id: '#chart_bot_caption_stats',
            data: LINE_BOT_Stats.TotalByCaption.data,
            date_start: LINE_BOT_Stats.TotalByCaption.date_start,
            date_end: LINE_BOT_Stats.TotalByCaption.date_end,
          });
          clearInterval(interval)
        }, 500)

        number_follower.then((result) => {
          console.log(result);

          if(result.status !== 200){
            return false;
          }

          document.getElementById('bot-status').innerHTML = `สถานะ : <span class="text-dark"> ${result.data.status}</span>`;
          document.getElementById('bot-followers').innerHTML = `จำนวนผู้ติดตาม : <span class="text-dark"> ${result.data.followers?result.data.followers.toLocaleString('th-TH'):'-'}</span>`;
          document.getElementById('bot-targetedReaches').innerHTML = `จำนวนผู้ใช้งานที่เข้าถึงได้ : <span class="text-dark"> ${result.data.targetedReaches?result.data.targetedReaches.toLocaleString('th-TH'):'-'}</span>`;
          document.getElementById('bot-blocks').innerHTML = `จำนวนที่ถูกบล็อค : <span class="text-dark"> ${result.data.blocks?result.data.blocks.toLocaleString('th-TH'):'-'}</span>`;

          return true;
        }).catch((err) => {
          console.log(err);
        }).finally(() => {
          console.log('json_example');
        }); 

      })

      function ConvertDateTH(date) {
        let d_Obj = new Date(date);
        return d_Obj.toLocaleDateString('th-TH', { 
          year: 'numeric',
          month: 'long',
          day: 'numeric' 
        });
      }

      function DrawChartLINEBotStats(parameter={}){

        let div_id = parameter.id;;
        let data = [];
        let categories = [];

        parameter.data.forEach((dataS, index) => {

          if(index > 9){
            return;
          }

          data.push(dataS.totalResponse?dataS.totalResponse:dataS.totalCaption)
          categories.push(dataS.title?dataS.title:(dataS.caption?dataS.caption:'ไม่ระบุ'))

        })

        let TitleText = `ข้อมูล จากวันที่ ${ConvertDateTH(parameter.date_start)} ถึง ${ConvertDateTH(parameter.date_end)}`;

        var options = {
            series: [{
            name: 'จำนวน',
            data: data
          }],
            chart: {
            height: 350,
            type: 'bar',
          },
          plotOptions: {
            bar: {
              borderRadius: 10,
              dataLabels: {
                position: 'top', // top, center, bottom
              },
            }
          },
          dataLabels: {
            enabled: true,
            formatter: (val) => {
              return val + " ครั้ง";
            },
            offsetY: -20,
            style: {
              fontSize: '12px',
              colors: ["#304758"]
            }
          },
          xaxis: {
            categories: categories,
            position: 'top',
            axisBorder: {
              show: false
            },
            axisTicks: {
              show: false
            },
            crosshairs: {
              fill: {
                type: 'gradient',
                gradient: {
                  colorFrom: '#000000',
                  colorTo: '#FFFFFF',
                  stops: [0, 100],
                  opacityFrom: 0.4,
                  opacityTo: 0.5,
                }
              }
            },
            tooltip: {
              enabled: false,
            }
          },
          yaxis: {
            axisBorder: {
              show: false
            },
            axisTicks: {
              show: false,
            },
            offsetX: -15,
            labels: {
              show: true,
              formatter: (val) => {
                return val + " ครั้ง";
              }
            }
          },
          title: {
            text: TitleText,
            floating: true,
            offsetY: 330,
            align: 'center',
            style: {
              color: '#444'
            }
          }
        };

        var chart = new ApexCharts(document.querySelector(div_id), options);
        chart.render();
      
      }

      function getLINEBotStats(){
        let TotalByResponse = getTotalByResponse();
        let TotalByCaption = getTotalByCaption();
        
        TotalByResponse.then(result => {
          console.log(result)
          LINE_BOT_Stats.TotalByResponse = result;
        })
        .catch(err => {
          console.error(err)
        })
        
        TotalByCaption.then(result => {
          console.log(result)
          LINE_BOT_Stats.TotalByCaption = result;
        })
        .catch(err => {
          console.error(err)
        })
      }

      function initsubscriptionPeriods(action=''){
        if(action == 'refresh') {
          DemoGraphics = getDemoGraphics();
        }
        DemoGraphics.then((result) => {
 
          if(!result.available){
            return false;
          }

          let data = result.subscriptionPeriods;
          let data_series = [];
          let data_labels = [];
          let color = ['#9055fd', '#56CA00', '#16B1FF', '#FFC107', '#FF9800', '#FF5722', '#795548', '#607D8B'];
          let colors = []
          data.forEach((data, i) => {

            let subscriptionPeriod = data.subscriptionPeriod;
            /* switch (data.age) {
              case 'male':
                age = 'ชาย'
                break;
              case 'female':
                age = 'หญิง'
                break;
              default:
                age = 'ไม่ระบุ'
                break;
            } */
            data_series.push(data.percentage);
            data_labels.push(subscriptionPeriod);
            colors.push(color[i])
            //document.getElementById(`appType_${app.appType}`).innerHTML = app.percentage + "%";
          })

          let options = {
            id: '#chart_subscriptionPeriods',
            series: data_series,
            labels: data_labels,
            formatter: function (val, opts) {
              return val + "%";
            },
            chart: {
              type: 'donut',
              height: 300 ,
              event: {
                beforeMount: function (chartContext, config) {
                  document.getElementById(options.id).innerHTML = '';
                }
              }
            },
            fill: {
              colors: colors
            }
          }

          if(action == 'refresh') {
            ApexCharts.exec(options.id, 'updateSeries', appType_series, true);
            return;
          }

          Chart_AppTypes = RenderDonutChart(options)
          console.log(Chart_AppTypes)

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });
      }

      function initAges(action=''){

        if(action == 'refresh') {
          DemoGraphics = getDemoGraphics();
        }

        DemoGraphics.then((result) => {
          console.log(result);

          if(!result.available){
            return false;
          }

          let data = result.ages;
          let data_series = [];
          let data_labels = [];
          let color = ['#9055fd', '#56CA00', '#16B1FF', '#FFC107', '#FF9800', '#FF5722', '#795548', '#607D8B'];
          data.forEach((data, i) => {
            let age = data.age;
            /* switch (data.age) {
              case 'male':
                age = 'ชาย'
                break;
              case 'female':
                age = 'หญิง'
                break;
              default:
                age = 'ไม่ระบุ'
                break;
            } */
            data_series.push(data.percentage);
            data_labels.push(age);
            //document.getElementById(`appType_${app.appType}`).innerHTML = app.percentage + "%";
          })

          let options = {
            id: '#chart_age',
            series: data_series,
            labels: data_labels,
            formatter: function (val, opts) {
              return val + "%";
            },
            chart: {
              type: 'donut',
              height: 300 ,
              event: {
                beforeMount: function (chartContext, config) {
                  document.getElementById(options.id).innerHTML = '';
                }
              }
            },
            fill: {
              colors: color
            }
          }

          if(action == 'refresh') {
            ApexCharts.exec(options.id, 'updateSeries', appType_series, true);
            return;
          }

          Chart_AppTypes = RenderDonutChart(options)
          console.log(Chart_AppTypes)

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });  
      }

      function initGenders(action=''){

        if(action == 'refresh') {
          DemoGraphics = getDemoGraphics();
        }

        DemoGraphics.then((result) => {
          console.log(result);

          if(!result.available){
            return false;
          }

          let genders = result.genders;
          let data_series = [];
          let data_labels = [];

          genders.forEach(data => {
            let gender = '';
            switch (data.gender) {
              case 'male':
                gender = 'ชาย'
                break;
              case 'female':
                gender = 'หญิง'
                break;
              default:
                gender = 'ไม่ระบุ'
                break;
            }

            document.getElementById(`gender_${data.gender}`).innerHTML = `${data.percentage}%`
            data_series.push(data.percentage);
            data_labels.push(gender);
            //document.getElementById(`appType_${app.appType}`).innerHTML = app.percentage + "%";
          })

          let options = {
            id: '#chart_genders',
            series: data_series,
            labels: data_labels,
            formatter: function (val, opts) {
              return val + "%";
            },
            chart: {
              type: 'donut',
              height: 300 ,
              event: {
                beforeMount: function (chartContext, config) {
                  document.getElementById(options.id).innerHTML = '';
                }
              }
            },
            fill: {
              colors: ['#9055fd', '#56CA00', '#16B1FF']
            }
          }

          if(action == 'refresh') {
            ApexCharts.exec(options.id, 'updateSeries', appType_series, true);
            return;
          }

          Chart_AppTypes = RenderDonutChart(options)
          console.log(Chart_AppTypes)

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });  
      }

      function initAreas(action=''){

        if(action == 'refresh') {
          DemoGraphics = getDemoGraphics();
        }

        DemoGraphics.then((result) => {
          console.log(result);

          if(!result.available){
            return false;
          }

          let areas = result.areas;
          let data_series = [];
          let data_labels = [];

          areas.forEach(data => {
            let area = '';
            /* switch (data.area) {
              case 'male':
                gender = 'ชาย'
                break;
              case 'female':
                gender = 'หญิง'
                break;
              default:
                gender = 'ไม่ระบุ'
                break;
            } */

            //document.getElementById(`gender_${data.area}`).innerHTML = `${data.percentage}%`
            data_series.push(data.percentage);
            data_labels.push(data.area);
            //document.getElementById(`appType_${app.appType}`).innerHTML = app.percentage + "%";
          })

          let options = {
            id: '#chart_areas',
            series: data_series,
            labels: data_labels,
            formatter: function (val, opts) {
              return Number(val).toFixed(2) + "%";
            },
            chart: {
              type: 'pie',
              height: 300 ,
              event: {
                beforeMount: function (chartContext, config) {
                  document.getElementById(options.id).innerHTML = '';
                }
              }
            },
            fill: {
              //colors: ['#9055fd', '#56CA00', '#16B1FF']
            }
          }

          if(action == 'refresh') {
            ApexCharts.exec(options.id, 'updateSeries', appType_series, true);
            return;
          }

          Chart_AppTypes = RenderDonutChart(options)
          console.log(Chart_AppTypes)

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });  
      }

      function initAppType(action=''){

        if(action == 'refresh') {
          DemoGraphics = getDemoGraphics();
        }

        DemoGraphics.then((result) => {
          console.log(result);

          if(!result.available){
            return false;
          }

          let appType = result.appTypes;
          let appType_series = [];
          let appType_labels = [];

          appType.forEach(app => {
            appType_series.push(app.percentage);
            appType_labels.push(app.appType);
            document.getElementById(`appType_${app.appType}`).innerHTML = app.percentage + "%";
          })

          let options = {
            id: '#chart_AppType',
            series: appType_series,
            labels: appType_labels,
            formatter: function (val, opts) {
              return val + "%"
            },
            chart: {
              type: 'donut',
              height: 300 ,
              event: {
                beforeMount: function (chartContext, config) {
                  document.getElementById(options.id).innerHTML = '';
                }
              }
            },
            fill: {
              colors: ['#9055fd', '#56CA00', '#16B1FF']
            }
          }

          if(action == 'refresh') {
            ApexCharts.exec(options.id, 'updateSeries', appType_series, true);
            return;
          }

          Chart_AppTypes = RenderDonutChart(options)
          console.log(Chart_AppTypes)

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });  
      }

      function initBotInfo(){
        bot_info = genBotInfo();
        bot_info.then((result) => {
          console.log(result);
          if(result.status !== 200){
            return false;
          }

          document.getElementById('bot-displayName').innerHTML = result.data.displayName;
          document.getElementById('bot-pictureUrl').src = result.data.pictureUrl;

          //url encode


          let url_encode = encodeURIComponent(result.data.basicId);
          document.getElementById('bot-basicId').innerHTML = `LINE ID : <a target="_blank" href="https://line.me/R/ti/p/${url_encode}">${result.data.basicId}</a>`;

        }).catch((err) => {
          console.log(err);
        }).finally(() => {

        });
      }

      async function getDemoGraphics() {
        <?php 
          if($Dummy_DATA){
            echo 'return json_example;';
          }
        ?>
        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            redirect: 'manual'
        };
        let response = await fetch("<?php echo $API_URL ."/get-demographics"; ?>", requestOptions);
        const status = response.status;
        if(status !== 200){
            return false;
        }

        const result = await response.json();
        return result;
      }

      async function getNumberFollwer(){

        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            redirect: 'manual'
        };
        let response = await fetch("<?php echo $API_URL ."/get-number-follower"; ?>", requestOptions);
        const status = response.status;
        if(status !== 200){
            return false;
        }

        const result = await response.json();
        return result;
      }

      async function getTotalByResponse(){

        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            redirect: 'manual'
        };
        let response = await fetch("<?php echo $API_URL ."/count-by-response"; ?>", requestOptions);
        const status = response.status;
        if(status !== 200){
            return false;
        }

        const result = await response.json();
        return result;
      }

      async function getTotalByCaption(){

        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            redirect: 'manual'
        };
        let response = await fetch("<?php echo $API_URL ."/count-by-caption"; ?>", requestOptions);
        const status = response.status;
        if(status !== 200){
            return false;
        }

        const result = await response.json();
        return result;
      }

      async function genBotInfo(){
        /* return {
            "status": 200,
            "message": "OK",
            "data": {
                "userId": "Ud94c93ded2ea4b032aa278f84ffe10af",
                "basicId": "@896wnguz",
                "displayName": "xDark-M",
                "pictureUrl": "https:\/\/profile.line-scdn.net\/0huEUqVR9eKmpoCDlc9ypVPVRNJAcfJiwiEG03XkpafAoSbD1vXGZsC0sBI1MSPD1sUmxsDR8MJFIW",
                "chatMode": "bot",
                "markAsReadMode": "auto"
            }
        }; */
        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            redirect: 'manual'
        };
        let response = await fetch("<?php echo $API_URL ."/get-bot-info"; ?>", requestOptions);
        const status = response.status;
        if(status !== 200){
            return false;
        }
        const result = await response.json();
        return result;
      }

      function RenderDonutChart(params){
        let element_selector = document.querySelector(params.id),
            series = params.series,
            labels = params.labels
            formatter = params.formatter?params.formatter:null;
            chart = params.chart?params.chart:{
              type: 'donut',
              height: 200
            },
            fill = params.fill?params.fill:{
              colors: ['#9055fd', '#56CA00', '#16B1FF']
            };

        var options = {
          series: series,
          labels: labels,
          xaxis: {
            type: 'category'
          },
          chart: chart,
          fill: fill,
          dataLabels: {
            enabled: true,
            enabledOnSeries: false,
            formatter: formatter,
            textAnchor: 'middle',
            distributed: false,
            offsetX: 0,
            offsetY: 0,
            style: {
                fontSize: '14px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 'bold',
                colors: ['#383838']
            },
            background: {
              enabled: true,
              foreColor: '#fff',
              padding: 4,
              borderRadius: 2,
              borderWidth: 1,
              borderColor: '#fff',
              opacity: 0.9,
              dropShadow: {
                enabled: false,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.45
              }
            },
            dropShadow: {
                enabled: false,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.45
            }
          },
          responsive: [
            {
              breakpoint: 480,
              options: {
                chart: {
                  width: '100%',
                  height: '100%'
                },
                legend: {
                  position: 'bottom'
                }
              }
            }
          ],
          
        };

        var chart = new ApexCharts(element_selector, options);
        chart.render();

        return chart;
      }
    </script>
  </body>
</html>
