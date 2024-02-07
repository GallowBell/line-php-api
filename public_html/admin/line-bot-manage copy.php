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

    <script src="<?php echo ASSET_URL('/js/admin/user_manage.js'); ?>"></script>
    
    
    <script>

        let table_bot_title;

        //css transition delay
        const set_css_transition = 350;
        const input_type = document.getElementById('type');

        const toastElList = new bootstrap.Toast('#basic-toast', {})
    

        document.addEventListener('DOMContentLoaded', (event) => {
            initDataTable();

            document.getElementById('data_response').addEventListener('input', (event) => {
                let input =event.target
                let value = input.value;
                let is_json = isJson(value);
                let type =document.getElementById('type')
                let change_event = new Event('change');
                if(is_json){
                    type.value = 'flex'
                    input_type.dispatchEvent(change_event);
                    return;
                }
                input_type.dispatchEvent(change_event);
                type.value = 'text'
                return;
            })

            document.getElementById('is_use_time').addEventListener('change', (event) => {
                let checked = event.target.checked;
                let input_time_response = document.getElementById('input-time-response');
                let response_time_start = document.getElementById('response_time_start')
                let response_time_end = document.getElementById('response_time_end')

                if(checked){
                    input_time_response.classList.remove('d-none')
                    response_time_start.removeAttribute('required')
                    response_time_end.removeAttribute('required')
                    return;
                }

                input_time_response.classList.add('d-none')

                response_time_start.value = '';
                response_time_end.value = '';
                response_time_start.setAttribute('required', true)
                response_time_end.setAttribute('required', true)
                return;
            })

            //set flatpickr
            flatpickr("#response_time_start", {
                onChange: function(selectedDates, dateStr, instance) {
                    console.log(selectedDates, dateStr, instance)
                },
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
                //defaultDate: data.start_time?data.start_time:''
            });
            flatpickr("#response_time_end", {
                onChange: function(selectedDates, dateStr, instance) {
                    console.log(selectedDates, dateStr, instance)
                },
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
                //defaultDate: data.end_time?data.end_time:''
            });

            $('#caption').on('select2:select', function (e) {
                //clear search value
                console.log(e)
                $('.select2-search__field').val('')               

            })


        });

        document.getElementById('save-bot-response').addEventListener('submit', async (event) => {
            event.preventDefault();
            let target = event.target;
            let action = target.dataset.action;
            let content_table = document.getElementById('content-table');

            Swal.fire({
                icon:'question',
                title: 'ยืนยันการบันทึกข้อมูล ?',
                text: 'ข้อมูลที่คุณกรอกจะถูกบันทึกลงในระบบ',
                showCancelButton: true
            }).then((result) => {

                if(!result.isConfirmed){
                    throw 'cancel';
                }

            }).then(() => {

                if(action == 'edit') {
                    let result = ActionEditForm(event);
                    let check_dnone = setInterval(() => {
                        if(!content_table.classList.contains('d-none')){
                            clearInterval(check_dnone)
                            initDataTable();
                            return;
                        }
                    }, 250);

                    return;
                }

                if(action == 'add') {
                    let result = ActionAddForm(event);
                    let check_dnone = setInterval(() => {
                        if(!content_table.classList.contains('d-none')){
                            clearInterval(check_dnone)
                            initDataTable();
                            return;
                        }
                    }, 250);
                    return;
                }

            }).catch(err => {
                console.error(err)
            })
            

            

        })

        async function ActionAddForm(event) {
            let target = event.target;
            let action = target.dataset.action
            let form = new FormData(event.target);
            let captions = $('#caption').val();
            if(captions.length <= 0) {
                Swal.fire({
                    icon:'warning',
                    title: 'Warning',
                    text: 'กรุณาเลือกข้อความที่ตรวจจับ',
                })
                return await false;
            }

            captions.forEach((caption, index) => {
                form.append(`caption[${index}]`, caption)
            })

            let data = Object.fromEntries(form.entries());
            console.log(data)

            let result = await addBotResponse(form);
            console.log(result)
            if(result.status != 200) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่อีกครั้ง',
                })
                return await false;
            }

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'บันทึกข้อมูลสำเร็จ',
            }).then(async (result) => {
                let res_close = await CloseDetail('finished')
                initDataTable();
            }).catch((err) => {
                console.error(err)
            });
            

        }

        async function ActionEditForm(event){
            let target = event.target;
            let action = target.dataset.action
            
            let form = new FormData(event.target);
            let id = event.submitter.dataset.id;

            form.set('id', id)

            let captions = $('#caption').val();
            if(captions.length <= 0) {
                Swal.fire({
                    icon:'warning',
                    title: 'Warning',
                    text: 'กรุณาเลือกข้อความที่ตรวจจับ',
                })
                return;
            }

            captions.forEach((caption, index) => {
                form.append(`caption[${index}]`, caption)
            })

            let result = await editBotResponse(form)

            if(result.status != 200){
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่อีกครั้ง',
                })
                console.error(result)
                return;
            }

            swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'บันทึกข้อมูลสำเร็จ',
            }).then((result) => {
                CloseDetail()
            }).catch((err) => {
                console.error(err)
            });
            return;
        }

        input_type.addEventListener('change', (event) => {
            console.log(event)
            let select =event.target
            let value = select.value;
            let altText_input = document.getElementById('altText-input')
            if(value == 'flex'){
                altText_input.classList.remove('d-none')
                altText_input.setAttribute('required', true)
                return;
            }
            altText_input.classList.add('d-none')
            altText_input.removeAttribute('required')
        })

        async function CloseDetail(action=false){

            let prom = new Promise((resolve, reject) => {
                
                if(document.getElementById('save-bot-response').dataset.action == 'add'){
                    
                    if(action == 'finished') {
                        resolve(true)
                        return true;
                    }

                    Swal.fire({
                        icon: 'question',
                        title: 'ยืนยันที่จะย้อนกลับ ?',
                        text: 'ข้อมูลที่คุณกรอกจะหายไปทั้งหมด',
                        showCancelButton: true,
                    }).then(swal_resl => {
                        if(!swal_resl.isConfirmed){
                            resolve(false)
                            return false;
                        }
                        resolve(true)
                        return true;
                    })
                }else{

                    resolve(true)
                    return true;
                }
               
            })
            
            let check_accept = await prom;
            if(!check_accept){
                return false;
            }

            document.getElementById('content-table').classList.remove('hide')
            document.getElementById('content-setting-response').classList.add('hide')
            document.getElementById('event_type').options.selectedIndex = 0;

            let css_transition = new Promise(resolve => {
                setTimeout(() => {
                    document.getElementById('content-table').classList.remove('d-none')
                    document.getElementById('content-setting-response').classList.add('d-none')
                    resolve(true)
                }, set_css_transition)
            })
            let result = await css_transition;
            return result;
        }

        async function ShowAddForm(){
            document.getElementById('content-table').classList.add('hide')
            document.getElementById('content-setting-response').classList.remove('hide')
            document.getElementById('save-bot-response').reset();
            $('#caption').val(null).trigger("change");
            document.getElementById('save-bot-response').dataset.action = 'add';

            // Set Value 
            flatpickr("#response_time_start", {
                onChange: function(selectedDates, dateStr, instance) {
                    console.log(selectedDates, dateStr, instance)
                },
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
            });
            flatpickr("#response_time_end", {
                onChange: function(selectedDates, dateStr, instance) {
                    console.log(selectedDates, dateStr, instance)
                },
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
            });

            $( '#caption' ).select2( {
                theme: 'bootstrap-5',
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                closeOnSelect: false,
                tags: true,
                createTag: (params) => {                    
                    return {
                        id: params.term,
                        text: params.term,
                        newOption: true
                    }
                },
                templateResult: (data, container) => {

                    let is_regex;
                    let check_is_regex = document.getElementById('is_regex').checked?1:0;

                    if(data.element){
                        is_regex = data.element.dataset.is_regex?data.element.dataset.is_regex:check_is_regex;
                    }

                    let $result = $("<div></div>");
                    $result.html(`<div class="d-flex justify-content-between">
                        <span>${data.text}</span>
                        ${data.newOption?`<span class="badge bg-label-success">NEW</span>`:``}
                    </div>`);
                    
                    return $result;
                }
            } );
            let css_transition = new Promise(resolve => {
                setTimeout(() => {
                    hideLoading()
                    document.getElementById('content-table').classList.add('d-none')
                    document.getElementById('content-setting-response').classList.remove('d-none')
                    resolve(true)
                }, set_css_transition);
            })
            let result = await css_transition;
            return result;
        }

        async function ShowEditForm(){

            document.getElementById('content-table').classList.add('hide')
            document.getElementById('content-setting-response').classList.remove('hide')
            document.getElementById('save-bot-response').dataset.action = 'edit';

            setTimeout(() => {
                hideLoading()
                document.getElementById('content-table').classList.add('d-none')
                document.getElementById('content-setting-response').classList.remove('d-none')
            }, set_css_transition);
            
            let A = event.target?event.target:false;
            let ID = A.dataset.id?A.dataset.id:false;

            console.log(ID)
            
            let filtered = filterDataFromDataTable('id', ID)
            
            if(filtered.length == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ไม่พบข้อมูล โปรดรีเฟรชแล้วลองใหม่อีกครั้ง',
                })
                return;
            }

            let data = filtered[0]
            console.log('data', data)
            document.getElementById('btn-save-bot-edit').dataset.id = ID;
            document.getElementById('test-sent-message').dataset.id = ID;
            document.getElementById('btn-delete-close').dataset.id = ID;
            

            let relativeTime = moment(data.last_update, 'YYYY-MM-DD HH:mm:ss').fromNow();
            let DateTimeTH = moment(data.last_update).format('LLLL');

            let formData = new FormData()
            formData.set('id', ID)

            let res = await getBotCaption(formData)

            console.log('data.start_time', data.start_time)
            console.log('data.end_time', data.end_time)
            // Set Value

            let input_time_response = document.getElementById('input-time-response');
            let response_time_start = document.getElementById('response_time_start')
            let response_time_end = document.getElementById('response_time_end')
            
            if(data.is_use_time){
                document.getElementById('is_use_time').checked = true;
                input_time_response.classList.remove('d-none')
                response_time_start.value = data.start_time?data.start_time:'';
                response_time_end.value = data.end_time?data.end_time:'';
                response_time_start.setAttribute('required', true)
                response_time_end.setAttribute('required', true)
            }else{
                document.getElementById('is_use_time').checked = false;
                response_time_start.removeAttribute('required')
                response_time_end.removeAttribute('required')
                input_time_response.classList.add('d-none')
                response_time_start.value = '';
                response_time_end.value = '';
            }

            if(data.is_use_ai){
                document.getElementById('is_use_ai').checked = true;
            }else{
                document.getElementById('is_use_ai').checked = false;
            }
            
            document.getElementById("title-detail").innerHTML = data.title;
            document.getElementById('last_updated').innerHTML = `<span title="${DateTimeTH?DateTimeTH:''}">${relativeTime?relativeTime:''}</span>`;
            document.getElementById('caption').innerHTML = '';
            document.getElementById('response_title').value = data.title;
            document.getElementById('type').value = data.type;
            document.getElementById('data_response').value = data.data_response?data.data_response:'';
            document.getElementById('altText').value = data.altText?data.altText:'';
            document.getElementById('event_type').value = res[0].event_type;

            let change_event = new Event('change');
            input_type.dispatchEvent(change_event);

            let check_is_regex = document.getElementById('is_regex');
            res.forEach((item) => {
                let is_regex = Number(item.is_regex);
                check_is_regex.checked = is_regex?true:false;
                let option = document.createElement('option')
                option.value = item.caption
                option.text = item.caption
                option.selected = true;
                option.dataset.is_regex = is_regex;
                document.getElementById('caption').appendChild(option)
            })

            $('#caption').select2( {
                theme: 'bootstrap-5',
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                closeOnSelect: false,
                tags: true,
                createTag: (params) => {
                    
                    return {
                        id: params.term,
                        text: params.term,
                        newOption: true
                    }
                },
                templateResult: (data, container) => {

                    let is_regex;
                    let check_is_regex = document.getElementById('is_regex').checked?1:0;

                    if(data.element){
                        is_regex = data.element.dataset.is_regex?data.element.dataset.is_regex:check_is_regex;
                    }

                    let $result = $("<div></div>");
                    $result.html(`<div class="d-flex justify-content-between">
                        <span>${data.text}</span>
                        ${data.newOption?`<span class="badge bg-label-success">NEW</span>`:``}
                    </div>`);
                    
                    return $result;
                }
            } );

        }

        

        //function filter data from DataTable
        function filterDataFromDataTable(key, value){
            let result = getAjaxFromDataTable().filter((item) => {
                return item[key] == value;
            })
            return result;
        }

        //function get ajax from DataTable
        function getAjaxFromDataTable(table="#table_bot_title"){
            let ajax = $(table).DataTable().ajax.json().data;
            return ajax;
        }

        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        /**
         * initialize DataTable
         * @function initDataTable
         */
        function initDataTable(){

            //destroy table
            $('#table_bot_title').DataTable().clear().destroy();
            showLoading();

            table_bot_title = new DataTable('#table_bot_title', {
                ajax: {
                    url: '<?php echo $APP_URL; ?>/line/api/get-bot-response',
                    type: 'POST',
                },
                processing: true,
                serverSide: true,
                screenX: '100%',
                scrollX: true,
                scrollY: '75vh',
                sScrollX: "100%",
                autoWidth: true,
                sScrollXInner: "100%",
                scrollCollapse: true,
                deferRender: true,
                scroller: {
                    loadingIndicator: true
                },
                buttons: [
                    {
                        text: 'Add New',
                        action: function ( e, dt, node, config ) {
                            //dt.ajax.reload();
                            let result = ShowAddForm();
                            console.log(result)
                        },
                        className: 'btn btn-primary btn-sm waves-effect',
                        attr: {
                            id: 'btn_add_response',
                            title: 'เพิ่มข้อมูลใหม่'
                            /* 'data-bs-target': '#basicModal',
                            'data-bs-toggle': 'modal' */
                        }
                    },
                    {
                        text: '<div title="Refresh"><span class="mdi mdi-refresh"></span></div>',
                        action: function ( e, dt, node, config ) {
                            dt.ajax.reload();
                        },
                        className: 'btn btn-primary btn-sm waves-effect'
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Thai.json"
                },
                columns: [
                    { 
                        data: 'title',
                        render: (data, type, row) => {
                            let result = data?data:'-';
                            let id = row.id?row.id:''
                            let link = ` href="javascript:void(0)" title="ดูข้อมูลเพิ่มเติม" onclick="ShowEditForm(this)" data-id="${id}" `
                            let html = `<div class="col-12">
                                <span class="text-center text-truncate text-primary " ${link}>
                                    <a ${link}>${result}</a>
                                </span>
                            </div>`
                            return html;
                        }
                    },
                    { 
                        data: 'altText',
                        render: (data, type, row) => {
                            let result = data?data:'-';
                            let html = `<div class="col-12">
                                <span class="text-center text-truncate">
                                    ${result}
                                </span>
                            </div>`
                            return html;
                        }
                    },
                    { 
                        data: 'event_type',
                        render: (data, type, row) => {
                            let result = data?data.toLocaleUpperCase():'-';
                            let html = `<div class="col-12 d-flex justify-content-center">
                                <span class="badge bg-info">
                                    ${result}
                                </span>
                            </div>`
                            return html;
                        }
                    },
                    { 
                        data: 'type',
                        render: (data, type, row) => {
                            let result = data?data.toLocaleUpperCase():'-';
                            let html = `<div class="col-12 d-flex justify-content-center">
                                <span class="badge ${result=='TEXT'?`bg-info`:`bg-success`}">
                                    ${result}
                                </span>
                            </div>`
                            return html;
                        }
                    },
                    { 
                        data: 'response_count',
                        render: (data, type, row) => {
                            let result = data?data.toLocaleString('th-TH'):'-';
                            let html = `<div class="col-12 d-flex justify-content-center">
                                ${result}
                            </div>`
                            return html;
                        }
                    },
                    {
                        data: 'is_use_time',
                        render: (data, type, row) => {
                            let html = '';
                            if(data){
                                html = `<div class="col-12 d-flex justify-content-center">
                                    <span class="badge bg-label-success">ใช้งาน</span>
                                </div>`
                            }else{
                                html = `<div class="col-12 d-flex justify-content-center">
                                    <span class="badge bg-label-danger">ไม่ใช้งาน</span>
                                </div>`
                            }
                            return html;
                        }
                    },
                    { 
                        data: 'active',
                        render: (data, type, row) => {
                            let result = data?data:'-';
                            let result_text = result=='1'?'เปิด':'ปิด';
                            let id = row.id?row.id:'';
                            let is_checked = result=='1'?'checked':'';
                            let html = `<div class="col-12 d-flex justify-content-center">
                                <div class="form-check form-switch mb-2">
                                    <input 
                                        ${is_checked} 
                                        class="form-check-input form-active-status" 
                                        title="${result_text}" 
                                        type="checkbox" 
                                        data-id="${id}" 
                                        value="1" 
                                        id="flex-switch-${id}"
                                    >
                                </div>
                            </div>`
                            return html;
                        }
                    },
                    { 
                        data: 'id',
                        render: (data, type, row) => {

                            let created = row.created?convertToThaiDateTime(row.created):'-';

                            let id = data?data:'-';
                            let html = `
                            <div class="dropdown" style="z-index: 9999;" title="${created}">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a title="ดูข้อมูลเพิ่มเติม" onclick="ShowEditForm(this)" data-id="${id}" href="javascript:void(0)" class=" dropdown-item waves-effect">
                                        <i class="mdi mdi-eye-outline"></i> ดูข้อมูลเพิ่มเติม
                                    </a>
                                    <a title="ทดสอบส่ง" onclick="testPushMessage(this.dataset.id)" data-id="${id}" href="javascript:void(0)" class=" dropdown-item waves-effect">
                                        <i class="mdi mdi-send-circle-outline"></i> ทดสอบส่ง
                                    </a>
                                    <hr>
                                    <a title="ลบ" onclick="DeleteResponse(this.dataset.id)" data-id="${id}" href="javascript:void(0)" class="btn-danger  dropdown-item waves-effect">
                                        <i class="mdi mdi-trash-can-outline"></i> ลบ
                                    </a>
                                </div>
                            </div>`
                            return html;
                        }
                    },
                ],
                order: [[ 5, "desc" ]],
                //for show button 
                dom: `
                <"row" r
                    <"col-12 mb-3 py-1"B>
                    <"col-sm-12 col-md-6 my-1"l>
                    <"col-sm-12 col-md-6 my-1"f>
                    <"col-12 row"
                    <" my-2 text-center" t>
                    <"col-sm-12 col-md-6 mb-3"i>
                    <"col-sm-12 col-md-6 mb-3"p>
                    >
                >`,
                initComplete: (settings, json) => {

                }

            })
            // on DataTable Draw Data
            .on("draw.dt", function (e, dt, type, indexes) {
                console.log('draw.dt', e, dt, type, indexes)
                document.querySelectorAll('.form-active-status').forEach(checkBoxOnChange)
                hideLoading();
            })
            .on( 'xhr', function ( e, settings, json ) {
                console.log( 'Ajax event occurred. Returned data: ', json );
                
            } );
        }

        function convertToThaiDateTime(date) {
            const options = { 
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric' 
            };
            const thaiDate = new Date(date).toLocaleString('th-TH', options);
            //const [day, month, yearAndTime] = thaiDate.split('/');
            //const [year, time] = yearAndTime.split(', ');
            return `${thaiDate}`;
        }

        function checkBoxOnChange(checkbox){

            //remove event
            checkbox.removeEventListener('change', () => {});

            //add event
            checkbox.addEventListener('change', async (event) => {
                let id = checkbox.dataset.id;
                let value = checkbox.checked?1:0;
                let Labeltext =value?'เปิด':'ปิด';
                let formData = new FormData();
                formData.append('id', id);
                formData.append('active', value);
                let res = await activeBotResponse(formData);
                if(res.status != 200){
                    ShowToast({
                        title: 'Error',
                        body: 'Update Error',
                        icon: '<i class="mdi mdi-close-circle-outline me-2 text-danger"></i>'
                    })
                    return;
                }
                ShowToast({
                    title: 'Success',
                    body: `${Labeltext}สำเร็จ`,
                    icon: '<i class="mdi mdi-check-circle-outline me-2 text-success"></i>'
                })
                event.target.title = Labeltext;
            })
        }

        async function editBotResponse(form){
            if(!(form instanceof FormData)){
                console.error('editBotResponse', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/edit-bot-response", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function CheckCaptionDuplicate(form){
            if(!(form instanceof FormData)){
                console.error('CheckCaptionDuplicate', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/check-caption-duplicate", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function addBotResponse(form){
            if(!(form instanceof FormData)){
                console.error('addBotResponse', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/add-bot-response", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function getBotCaption(form){
            if(!(form instanceof FormData)){
                console.error('getBotCaption', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/get-bot-caption", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function DeleteResponse(id){

            if(!id) {
                return;
            }

            //instanceof swal2
            let swal_res = await Swal.fire({
                icon: 'question',
                html: `คุณต้องการลบข้อมูลนี้ใช่หรือไม่ ?`,
                showCancelButton: true,
            })

            //check isConfirm
            if(!swal_res.isConfirmed){
                return;
            }

            //call api delete
            let formData = new FormData();
            formData.set('id', id);
            let res = await DeleteBotResponse(formData);
            if(res.status != 200){
                ShowToast({
                    title: 'Error',
                    body: 'Delete Error',
                    icon: '<i class="mdi mdi-close-circle-outline me-2 text-danger"></i>'
                })
                return;
            }
            ShowToast({
                title: 'Success',
                body: 'Delete Success',
                icon: '<i class="mdi mdi-check-circle-outline me-2 text-success"></i>'
            })
            let Close_res = await CloseDetail('finished')
            initDataTable();
            
        }

        async function getBotResponse(form){
            if(!(form instanceof FormData)){
                console.error('getBotResponse', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/get-bot-response", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function DeleteBotResponse(form) {
            if(!(form instanceof FormData)){
                console.error('DeleteBotResponse', 'first parameter must be FormData')
                return false;
            }
            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/delete-bot-response", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        async function activeBotResponse(form){

            if(!(form instanceof FormData)){
                console.error('activeBotResponse', 'first parameter must be FormData')
                return false;
            }

            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/active-bot-response", requestOptions);
            const status = response.status;
            if(status !== 200){
                return false;
            }

            const result = await response.json();
            return result;
        }

        ///test-push-message
        async function testPushMessage(id=''){
            
            let form = new FormData();
            let data_response = document.getElementById('data_response').value?document.getElementById('data_response').value:'';
            let type = document.getElementById('type').value?document.getElementById('type').value:'';
            console.log(event)
            form.set('id', id);
            form.set('data_response', data_response);
            form.set('type', type)

            let btn = event.target;

            //loop until btn is button
            while(btn.tagName != 'A' && btn.tagName != 'BUTTON'){
                btn = btn.parentElement;
            }

            if( !id && (!data_response || !type) ){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                })
                return;
            }

            
            //set bootstrap5 loading to btn
            btn.setAttribute('disabled', true)
            btn.innerHTML = `
                <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                <span role="status">Loading...</span>
            `;

            const myHeaders = new Headers();
            const requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: form,
                redirect: 'manual'
            };
            let response = await fetch("<?php echo $API_URL; ?>/test-push-message", requestOptions);
            const status = response.status;
            
            btn.removeAttribute('disabled')
            btn.innerHTML = `<span class="mdi mdi-check-bold mx-auto"></span></i> สำเร็จ`;
            setTimeout(() => {
                btn.innerHTML = `<i class="mdi mdi-send-circle-outline mx-auto"></i> ทดสอบส่ง`;
            }, 1000);
            
            if(status !== 200){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'เกิดข้อผิดพลาด ไม่สามารถทดสอบส่งข้อความได้ โปรดลองใหม่อีกครั้ง',
                })
                return false;
            }

            const result = await response.json();
            return result;
        }

        function ShowToast(params={
            title:"",
            body:"",
            icon:""
        }){
            let title = params.title?params.title:'';
            let body = params.body?params.body:'';
            let icon = params.icon?params.icon:'<i class="mdi mdi-home me-2 text-danger"></i>';
            document.getElementById('toast-header').innerHTML = title;
            document.getElementById('toast-body').innerHTML = body;
            document.getElementById('toast-header-icon').innerHTML = icon;
            toastElList.show();
        }
        
    </script>


  </body>
</html>
