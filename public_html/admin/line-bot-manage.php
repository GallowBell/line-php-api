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

    $db_EventType = $db_LINE->query("SELECT * FROM `line_event_type`");

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
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <?php include_once __DIR__ . '/../require_page/sub_header.php'; ?>

    <style>
        /* css sweet alert2 over modal */
        .swal2-container {
            z-index: 10000;
        }

        .pe-pointer {
            cursor: pointer;
        }
        /* css select2 over modal */
        .select2-container {
            z-index: 10000;
        }
        /* css select2 active or selected */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: linear-gradient(270deg, #9055fd 0%, #c4a5fe 100%);
            color: #fff;
        }
        .hide {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.35s ease-out, visibility 0.35s ease-out;
        }
        .select2-selection__choice {
            margin-top: 10px
        }
        .select2-results {
            display: none;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection, .select2-container--bootstrap-5.select2-container--open .select2-selection {
            /* border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); */
            border-color: #9055fd !important;
            border-width: 2px !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">LINE /</span> BOT Manage</h4>

                <div class="row" id="content-table">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" id="bot_manage">
                                    <div class="row">
                                        <h5 class="card-header">LINE Bot Manage</h5>
                                        <div class="col-12">
                                            <table id="table_bot_title" class="table table-bordered table-hover table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th title="ชื่อ" class="text-center text-nowrap">Title</th>
                                                        <th title="ข้อความในช่อง Notify" class="text-center text-nowrap">altText</th>
                                                        <th title="ประเภทของ Event" class="text-center text-nowrap">Event</th>
                                                        <th title="ประเภทของข้อความ" class="text-center text-nowrap">Type</th>
                                                        <th title="จำนวนการตอบ" class="text-center text-nowrap">Total</th>
                                                        <th title="กำหนดเวลา" class="text-center text-nowrap">Time Range</th>
                                                        <th title="สถานะใช้งาน" class="text-center text-nowrap">In Use</th>
                                                        <th title="จัดการ" class="text-center text-nowrap">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- / card-body -->
                        </div><!-- / card -->
                    </div><!-- / col-12 -->
                </div><!-- / row -->

                <div class="row d-none hide" id="content-setting-response">
                    <form id="save-bot-response" method="post" data-action="">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row d-flex justify-content-between">
                                        <div class="col">
                                            <div class="col-12">
                                                <h4 class="modal-title" id="exampleModalLabel1">รายละเอียด <span id="title-detail"></span></h4>
                                            </div>
                                            <div class="col-12">
                                                Last Updated <span id="last_updated"></span>
                                            </div>
                                        </div>
                                        <button type="button" title="ลบข้อมูล" onclick="DeleteResponse(this.dataset.id)" data-id="" id="btn-delete-close" class="btn-close p-3" aria-label="Close"></button>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-5 my-2">
                                            <h5>Title</h5>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <input required class="form-control" type="text" placeholder="Title" value="" name="response_title" id="response_title">
                                                <label for="response_title">Title</label>
                                                <p class="text-muted mt-2">Title ชื่อสำหรับการจัดการ ลูกค้าจะไม่เห็นสิ่งนี้</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12 my-2">
                                            <h5>Response Setting</h5>

                                            <!-- is regex -->
                                            <div class="form-check my-3 " title="ตรวจจับ Caption อยู่ในกลุ่มข้อความ หรือ เหมือน 100%  ">
                                                <input class="form-check-input" type="checkbox" value="1" name="is_regex" id="is_regex">
                                                <label class="form-check-label" for="is_regex"> 
                                                    อยู่ในกลุ่มข้อความ 
                                                    <span class="text-muted">ตรวจจับ Caption อยู่ในกลุ่มข้อความ หรือ เหมือน 100% หากติ๊กถูกคือ อยู่ในกลุ่มข้อความ </span>
                                                </label>
                                            </div>

                                            <!-- is use ai -->
                                            <div class="form-check my-3 " title="ใช้ AI ตอบกลับ ">
                                                <input class="form-check-input" type="checkbox" value="1" name="is_use_ai" id="is_use_ai">
                                                <label class="form-check-label" for="is_use_ai"> 
                                                    ใช้ AI ตอบกลับ 
                                                    <span class="text-muted">หากใช้งาน AI ข้อความในช่อง Message Text จะเป็นข้อความสำรองหากระบบ GPT ไม่สามารถส่งข้อความได้</span>
                                                </label>
                                            </div>

                                            <!-- caption -->
                                            <div class="form-floating form-floating-outline">
                                                <select 
                                                    required 
                                                    multiple 
                                                    class="form-select" 
                                                    id="caption" 
                                                    aria-label="ข้อความที่ตรวจจับ" 
                                                    data-placeholder="พิมข้อความแล้วกด Enter เพื่อเพิ่ม" 
                                                    placeholder="พิมข้อความแล้วกด Enter เพื่อเพิ่ม"
                                                    >
                                                </select>
                                                <label for="caption">ข้อความที่ตรวจจับ</label>
                                            </div>
                                            <p class="col-12 mb-4">
                                                <span class="text-muted">พิมพ์ข้อความแล้วกด Enter </span>
                                            </p>

                                            <!-- event -->
                                            <div class="form-floating form-floating-outline mb-4">
                                                <select required class="form-select" id="event_type" name="event_type" aria-label="ประเภท Event" placeholder="ประเภท Event">
                                                <?php 
                                                    foreach($db_EventType as $key => $value){
                                                        echo '<option value="'.$value['id'].'">'.strtoupper($value['event_type']).'</option>';
                                                    }
                                                ?>
                                                </select>
                                                <label for="event_type">ประเภท Event</label>
                                            </div>

                                            <!-- time setting -->
                                            <div class="form-check my-3 " title="ตอบในช่วงเวลา">
                                                <input class="form-check-input" type="checkbox" value="1" name="is_use_time" id="is_use_time">
                                                <label class="form-check-label" for="is_use_time"> 
                                                    ตอบในช่วงเวลา
                                                    <span class="text-muted">หากกำหนดเวลา บอทจะตอบข้อความระหว่าง เวลาเริ่ม กับ เวลาสิ้นสุด</span>
                                                </label>
                                            </div>
                                            <div class="input-group my-3 d-none" id="input-time-response">
                                                <div class="form-floating form-floating-outline mb-4">
                                                    <input class="form-control" type="text" placeholder="ชั่วโมง:นาที" value="" name="response_time_start" id="response_time_start">
                                                    <label for="response_time_start">ระยะเวลาเริ่ม</label>
                                                </div>
                                                <div class="form-floating form-floating-outline mb-4">
                                                    <input class="form-control" type="text" placeholder="ชั่วโมง:นาที" value="" name="response_time_end" id="response_time_end">
                                                    <label for="response_time_end">ระยะเวลาสิ้นสุด</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12 my-2">
                                            <h5>Message Setting</h5>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <select required class="form-select" id="type" name="type" aria-label="เลือกประเภทข้อความ">
                                                    <option selected hidden disabled value="">เลือกประเภทข้อความ</option>
                                                    <option value="flex">FLEX</option>
                                                    <option value="text">TEXT</option>
                                                </select>
                                                <label for="type">เลือกประเภทข้อความ</label>
                                            </div>
                                            <div class="form-floating form-floating-outline mb-4" id="altText-input">
                                                <input class="form-control mb-2" type="text" placeholder="Title" value="" name="altText" id="altText">
                                                <label for="altText">
                                                    Notify Message
                                                </label>
                                                <span class="text-muted">
                                                    ข้อความ Notify Message จะแสดงเฉพาะในการแจ้งเตือน และ ประเภทข้อความที่เป็น FLEX เท่านั้น
                                                    <a href="https://developers.line.biz/flex-simulator/">คลิกที่นี่เพื่อไปที่ เครื่องมือสร้าง FLEX Message</a>
                                                </span>
                                            </div>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <textarea required class="form-control h-px-200" name="data_response" id="data_response" placeholder="Message Text"></textarea>
                                                <label for="data_response">Message Text</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 my-2">
                                        <div class="row justify-content-between">
                                            <div class="col-6 d-flex justify-content-start">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-secondary waves-effect waves-light mx-1"
                                                    onclick="CloseDetail()"
                                                    >
                                                    <i class="mdi mdi-arrow-left mx-auto"></i> ย้อนกลับ
                                                </button>
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                <button 
                                                    type="button"
                                                    class="btn btn-info waves-effect waves-light mx-1" 
                                                    data-id="" 
                                                    onclick="testPushMessage()"
                                                    id="test-sent-message"
                                                    title="ทดสอบส่ง"
                                                    >
                                                    <i class="mdi mdi-send-circle-outline mx-auto"></i> ทดสอบส่ง
                                                </button>
                                                <button type="submit" class="btn btn-primary waves-effect" data-id="" id="btn-save-bot-edit" >
                                                    <i class="mdi mdi-content-save mx-auto"></i> บันทึกการเปลี่ยนแปลง
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- / card-body -->
                            </div><!-- / card -->
                        </div><!-- / col-12 -->
                    </form>
                </div><!-- / row -->
                
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

    <!-- Toast -->
    <div id="basic-toast" class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
        <div class="toast-header">
            <span id="toast-header-icon">
                <i class="mdi mdi-home me-2 text-danger"></i>
            </span>
            <div class="me-auto fw-medium" id="toast-header"></div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body  my-2" id="toast-body" >
        </div>
    </div>
    <!-- / Toast -->



    <!-- Modal -->
    <?php 
    /*
    <form id="form-modal">
      <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="basicModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content" >
            <div class="modal-header border-bottom pb-2">
              <div class="col">
                <div class="col-12">
                    <h4 class="modal-title" id="exampleModalLabel1">รายละเอียด <span id="title-detail"></span></h4>
                </div>
                <div class="col-12">
                    Last Updated <span id="last_updated"></span>
                </div>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div><!-- /modal-content -->
            <div class="modal-body" id="modal-body-detail" > 
              
            </div><!-- /modal-body -->
            <div class="modal-footer border-top pt-3" id="modal-footer-detail">
                
            </div><!-- /modal footer -->
          </div>
        </div>
      </div>
    </form>
    */
    ?>
    <!-- / Modal -->

    
    <?php require_once __DIR__ . '/../require_page/footer.php'; ?>

    <!-- Page JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js" integrity="sha512-42PE0rd+wZ2hNXftlM78BSehIGzezNeQuzihiBCvUEB3CVxHvsShF86wBWwQORNxNINlBPuq7rG4WWhNiTVHFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.min.js" integrity="sha512-IiSJKJyOVydT9/jfVcnpg7PIUM41Be6YzR5bTiAEAEQxTVtnUhbhiSNtgGXmOTFoxYpYs+LdxWlELOK7iRVVBg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/datatables.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script> 

    <script>
        const $APP_URL = "<?php echo $APP_URL; ?>";
        const $API_URL = "<?php echo $API_URL; ?>";
    </script>

    <script src="<?php echo ASSET_URL('/js/admin/line-bot-manage.js'); ?>"></script>
    
    
    

  </body>
</html>
