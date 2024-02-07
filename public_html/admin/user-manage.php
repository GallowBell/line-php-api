<?php 

    require_once __DIR__ . "/../config.php";
    require_once __DIR__ . "/middleware/index.php";

    if($UserData[0]['access_level'] < 100){
        header('Location: /');
        exit;
    }

    $DefaultImageProfile = ASSET_URL('/img/avatars/1.png');

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
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'); ?>" />
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/libs/apex-charts/apex-charts.css'); ?>" />

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

                <h4 class="py-3 mb-4"><span class="text-muted fw-light">User /</span> Management</h4>

                <!-- Data Tables -->
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-body">
                        <form action="" id="form_user">
                          <div class="row">
                            <div class="col-12">
                              <table class="table" id="table_user">
                                <thead class="table-dark">
                                  <tr>
                                    <th class="text-center text-truncate">LINE Profile</th>
                                    <th class="text-center text-truncate">ตำแหน่ง</th>
                                    <th class="text-center text-truncate">สถานะ</th>
                                    <th class="text-center text-truncate">ACTION</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                              </table>
                            </div>
                          </div><!--/ row  -->
                        </form> <!-- /form -->
                      </div><!-- /card-body -->
                    </div><!-- /card -->
                  </div>
                </div><!--/row  -->
                <!--/ Data Tables -->
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

    <!-- Modal -->
    <form id="add_new_user">
      <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="basicModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content" >
            <div class="modal-header border-bottom pb-3">
              <h4 class="modal-title" id="exampleModalLabel1">เพิ่ม User ใหม่</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div><!-- /modal-content -->
            <div class="modal-body" id="modal-body-add-new-user"> 
              
            </div><!-- /modal-body -->
            <div class="modal-footer">
              <button type="button" id="finished-btn" class="btn btn-primary waves-effect d-none"  onclick="ResetForm(true)">
                เสร็จสิ้น
              </button>
              <button type="submit" id="submit-btn" class="btn btn-primary waves-effect waves-light">
                สร้าง QR Code LOGIN
              </button>
              <button type="button" id="cancel_qr_code" onclick="CancelQRCode(this)" class="btn btn-secondary waves-effect waves-light d-none">
                ยกเลิก QR Code 
              </button>
            </div><!-- /modal footer -->
          </div>
        </div>
      </div>
    </form>

    <!-- / Modal -->

    <?php require_once __DIR__ . '/../require_page/footer.php'; ?>

    <!-- Page JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/datatables.min.js"></script>

    <!-- <script src="<?php echo ASSET_URL('/js/admin/user_manage.js'); ?>"></script> -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script>
      let interval_check,
          table_user,
          DomBasicModal = document.getElementById('basicModal'),
          BasicModal = new bootstrap.Modal(DomBasicModal, {
            keyboard: false
          });
      document.addEventListener('DOMContentLoaded', (event) => {

        ResetContentModal();
        const tables = initDataTable();

      });/* end DOMContentLoaded */

      document.getElementById("add_new_user").addEventListener("submit", (event) => {
        event.preventDefault();
        Swal.fire({
          icon: 'question',
          html: `คุณยืนยันที่จะเพิ่ม User ใหม่หรือไม่ ?`,
          showCancelButton: true,
        }).then(swal_res => {

          if (!swal_res.isConfirmed) {
            return false;
          }

          const form = new FormData(event.target);
          const data = Object.fromEntries(form.entries());

          //loop FormData
          for (const [key, value] of form.entries()) {
            if(!value){
              Swal.fire({
                icon: 'warning',
                html: `กรุณากรอกข้อมูลให้ครบถ้วน`
              })
              return false;
            }
          }

          const cancel_qr_code = document.getElementById('cancel_qr_code'),
            finished_btn = document.getElementById("finished-btn"),
            submit_btn = document.getElementById('submit-btn'),
            content_qr_login = document.getElementById('content_qr_login');

          ToggleForm(false)
          content_qr_login.innerHTML = `
            <div class="d-flex justify-content-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          `;

          let url_result = generateLoginURL(form);
          let id;
          url_result.then(result => {
            console.log('then #1', result)

            if(result.status !== 200){
              ToggleForm(true)
              content_qr_login.innerHTML = '';
              Swal.fire({
                icon: 'warning',
                html: `
                  <p>เกิดข้อผิดพลาดในการสร้าง QR Code กรุณาลองใหม่อีกครั้ง</p>
                  <p>${result.message}</p>`
              })
              throw 'error generate login url'
            }

            id = result.id;
            submit_btn.classList.add('d-none');
            cancel_qr_code.setAttribute('data-id', result.id);
            let url = result.url;
            return url;

          // callback generate qr code url
          }).then(result => {
            console.log('then #2', result)
            let formQR = new FormData();
            formQR.set('url', result);
            let qr_result = QRcode(formQR);
            return qr_result;
          })

          // callback show image base64
          .then(result => {
            console.log('then #3', result)
            let base64 = arrayBufferToBase64(result);
            let html = `
              <div class="col-12" >
                <div class="alert alert-info" role="alert">
                  <h4 class="alert-heading">QR Code</h4>
                  <p>สแกน QR Code เพื่อเข้าสู่ระบบ</p>
                </div>
              </div>
              <img src="data:image/png;base64,${base64}" alt="" class="card-img-top">
            `;

            content_qr_login.innerHTML = html;
            cancel_qr_code.classList.remove('d-none');
            finished_btn.classList.remove('d-none');

            let checkRegis_form = new FormData()
            console.log('id', id)
            checkRegis_form.set('id', id);
            //set interval using function checkRegister
            interval_check = setInterval(() => {
              let res = checkRegister(checkRegis_form);
              res.then(result => {
                console.log(result)
                if(!result.result){
                  return;
                }
                clearInterval(interval_check);
                Swal.fire({
                  icon: 'success',
                  html: 'สำเร็จ <p>User ถูกเพิ่มเรียบร้อยแล้ว</p>'
                })
                BasicModal.hide();
                initDataTable();
              }).catch(err => {
                console.error(err)
              })
            }, 5000);
            //clear interval            

          // error
          }).catch(err => {
            console.error(err)
            ToggleForm(true)
            document.getElementById('cancel_qr_code').classList.add('d-none');
            Swal.fire({
              icon: 'error',
              html: 'มีบางอย่างผิดพลาดโปรดลองใหม่อีกครั้ง'
            })
          })
         
        })/* end sweetalert2 */      
          
      })/* end form submit */

      function DeleteAction(id=''){
        Swal.fire({
          icon: 'question',
          html: `คุณต้องการลบผู้ใช้นี้หรือไม่ ?`,
          showCancelButton: true,
        }).then(swal_res => {
          if(!swal_res.isConfirmed){
            return false;
          }
          showLoading();
          let form = new FormData();
          form.set('id', id);
          let res = deleteUser(form);
          res.then(result => {
            hideLoading();
            console.log(result)
            if(result.status == 200){
              Swal.fire({
                icon: 'success',
                html: 'ลบผู้ใช้เรียบร้อยแล้ว'
              })
              initDataTable();
            }

          })
          .catch(err => {
            hideLoading();
            console.error(err)
          })
        })
      }
      
      function CancelQRCode(btn){
        Swal.fire({
          icon: 'question',
          html: `คุณยืนยันที่จะยกเลิก QR Code หรือไม่ ?`,
          showCancelButton: true,
        }).then(swal_res => {
          if (!swal_res.isConfirmed) {
            return false;
          }

          clearInterval(interval_check);
          let id = btn.dataset.id;
          let form = new FormData()
          form.set('id', id);

          let content_qr_login = document.getElementById('content_qr_login');

          //bootstrap5 alert div
          content_qr_login.innerHTML = `
            <div class="alert alert-info" role="alert">
              <h4 class="alert-heading">QR Code</h4>
              <p>กำลังยกเลิก QR Code</p>
              <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
          `;
         
          let res = CancelQRCodeURL(form)
          res.then(result => {
            console.log(result)

            if(result.status == 200) {
              setTimeout(() => {
                content_qr_login.innerHTML = `
                  <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">QR Code</h4>
                    <p>ยกเลิกเรียบร้อยแล้ว</p>
                  </div>
                `;
              }, 500);
              return ;
            }

            setTimeout(() => {
              content_qr_login.innerHTML = `
                <div class="alert alert-info" role="alert">
                  <h4 class="alert-heading">QR Code</h4>
                  <p>มีบางอย่างผิดพลาดโปรดลองใหม่อีกครั้ง</p>
                </div>
              `;
            }, 500);
            
          }).catch(err => {
            console.error(err)
            
          }).finally(() => {
            ToggleForm(true)
            ResetForm();
          })

        })
      }

      /**
       * getUser Data
       * @param {FormData} form 
       * @returns 
       */
      async function getUser(form){
          const myHeaders = new Headers();
          const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: form,
            redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?>/line/api/get-users", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200){
              return false;
          }
          const result = await fetching.json();
          return result;
      }

      /**
       * editUser Data /line/api/edit-users
       * @param {FormData} form 
       * @returns 
       */
      async function editUser(form) {
          const myHeaders = new Headers();
          const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: form,
            redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?>/line/api/edit-users", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200){
              return false;
          }
          const result = await fetching.json();
          return result;
      }

      /**
       * deleteUser Data /line/api/edit-users
       * @param {FormData} form id
       * @returns 
       */
      async function deleteUser(form) {
          const myHeaders = new Headers();
          const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: form,
            redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?>/line/api/delete-users", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200) {
            return false;
          }
          const result = await fetching.json();
          return result;
      }

      /**
       * checkRegister status
       * @param {FormData} form 
       * @returns 
       */
      async function checkRegister(form) {
          const myHeaders = new Headers();
          const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: form,
            redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?>/line/api/check-login", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200){
              return false;
          }
          const result = await fetching.json();
          return result;
      }

      /**
       * generate LoginURL
       * @param {FormData} form 
       * @returns 
       */
      async function CancelQRCodeURL(form){
          const myHeaders = new Headers();
          const requestOptions = {
              method: 'POST',
              headers: myHeaders,
              body: form,
              redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?>/line/delete", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200){
              return false;
          }
          const result = await fetching.json();
          return result;
      }
      
      /**
       * generate LoginURL
       * @param {FormData} form 
       * @returns 
       */
      async function generateLoginURL(form){
          const myHeaders = new Headers();
          const requestOptions = {
              method: 'POST',
              headers: myHeaders,
              body: form,
              redirect: 'manual'
          };
          const fetching = await fetch("<?php echo $APP_URL; ?><?php echo $_ENV['LINE_LOGIN_PATH']; ?>", requestOptions)
          //get status
          const status = fetching.status;
          if(status !== 200){
              return false;
          }
          const result = await fetching.json();
          return result;
      }

      /**
       * generate QR Code for login
       * @param {FormData} form 
       * @returns 
       */
      async function QRcode(form){
        const myHeaders = new Headers();
        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: form,
            redirect: 'manual'
        };
        const fetching = await fetch("<?php echo $APP_URL; ?>/line/api/qr-code-login", requestOptions)
        //get status
        const status = fetching.status;
        if(status !== 200){
            return false;
        }
        const result = await fetching.arrayBuffer();
        return result;
      }

      /**
       * function generate image from arrayBuffer
       * @param {ArrayBuffer} arrayBuffer
       * @returns {string} base64
       */
      function arrayBufferToBase64(arrayBuffer) {
        let binary = '';
        const bytes = [].slice.call(new Uint8Array(arrayBuffer));
        bytes.forEach((b) => binary += String.fromCharCode(b));
        return window.btoa(binary);
      };

      function initDataTable() {
          $('#table_user').DataTable().clear().destroy();

          let classRowName = 'row_id-';
          table_user = new DataTable('#table_user', {
              ajax: {
                url: '<?php echo $APP_URL; ?>/line/api/get-users',
                method: 'POST',
              },
              screenX: '100%',
              scrollX: true,
              scrollY: '75vh',
              sScrollX: "100%",
              sScrollXInner: "100%",
              scrollCollapse: true,
              deferRender: true,
              scroller: {
                  loadingIndicator: true
              },
              buttons:[
                {
                  text: `<span class="tf-icons mdi mdi-account-plus mx-1"> Add User</span>`,
                  action: function ( e, dt, node, config ) {
                   
                  },
                  attr: {
                    id: 'btn_add_user',
                    'data-bs-target': '#basicModal',
                    'data-bs-toggle': 'modal'
                  },
                  className: 'btn btn-primary waves-effect waves-light'
                }
              ],
              language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Thai.json"
              },
              columns: [
                { 
                    data: 'userId',
                    render: (data, type, row) => {
                      let pictureUrl = row.pictureUrl?row.pictureUrl:'<?php echo $DefaultImageProfile; ?>';
                      let fullname = `${row.fname?row.fname:''} ${row.lname?row.lname:''}`;
                      let result = data?`
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <img src="${pictureUrl}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div>
                          <h6 class="mb-0 text-center  text-truncate">${row.displayName?row.displayName:'-'}</h6>
                          <small class="text-center text-truncate">${fullname}</small>
                        </div>
                      </div>`:'-';

                      return result;
                    }
                },
                {
                  data: 'access_level',
                  render: (data, type, row) => {
                    let html = HTML_Access_Level(row);
                    return html;
                  } 
                },
                {
                  data: 'is_active',
                  render: (data, type, row) => {
                    let html = HTML_IS_Active(row);
                    return html;
                  } 
                },
                {
                  data: 'id',
                  render: (data, type, row) => {
                    let html = HTML_BTN_Action(row)
                    return html;
                  }
                },
              ],
              dom: `
              <"row"
                <"col-12 mb-3 py-1"B>
                <"col-sm-12 col-md-6 my-1"l>
                <"col-sm-12 col-md-6 my-1"f>
                <"col-12 row"
                  <" my-2 text-center" t>
                  <"col-sm-12 col-md-6 mb-3"i>
                  <"col-sm-12 col-md-6 mb-3"p>
                >
              >`,
              order: [[ 0, "desc" ]],
              initComplete: function(settings, json) {
                document.querySelectorAll('#table_user tbody tr').forEach(element => {
                  element.removeEventListener('click', (event) => {})
                  element.addEventListener('click', (event) => {
                    let pointerType = event.pointerType;
                    if(pointerType == 'mouse'){
                      return;
                    }

                    //loop target to get TR
                    let target = event.target;
                    while(target.tagName !== 'TR'){
                      target = target.parentElement;
                    }
                    document.querySelectorAll('.bg-light').forEach(bg => {
                      bg.classList.remove('bg-light');
                    })
                    target.classList.add('bg-light');

                  })
                })
              },
          });
        return table_user;
      }

      function HTML_Access_Level(params){
        let ondblclick = Number(params.access_level)==1000?'':'ondblclick="FormEditUser(1)"';
        let access_level = params.access_level?params.access_level:false;
        let result = `<div class="row_id-${params.id} pe-pointer" ${ondblclick} data-action='1' data-access_level="${access_level}" data-id="${params.id}">`;
        if(access_level == 1000){
          result += `<span class="badge bg-label-secondary rounded-pill " >Super Admin</span>`;
        }else if(access_level == 100){
          result += `<span class="badge bg-label-primary rounded-pill " >Admin</span>`;
        }else{
          result += `<span class="badge bg-label-success rounded-pill " >User</span>`;
        }
        result += '</div>';
        return result;
      }

      function HTML_IS_Active(params) {
        let ondblclick = Number(params.access_level)==1000?'':'ondblclick="FormEditUser(1)"';
        let result = params.is_active?params.is_active:'0';
        let className = result==1|result=='1'?'badge bg-label-primary rounded-pill':'badge bg-label-danger rounded-pill';
        let html = `
        <div class="row_id-${params.id} pe-pointer" data-action='2' ${ondblclick} data-is_active="${result}" data-id="${params.id}">
          <span class="${className} " >
            ${result==1|result=='1'?'Active':'Inactive'}
          </span>
        </div>`
        return html;
      }

      function HTML_BTN_Action(params){
        let id = params.id?params.id:'';
        let access_level = Number(params.access_level)==1000?'disabled':'';
        let html = `
        <div class="row_id-${id}" data-btn_action="action" data-id="${id}">
          <div class="dropdown" style="z-index: 9999;">
            <button type="button"  ${access_level} class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="mdi mdi-dots-vertical"></i>
            </button>
            <div class="dropdown-menu" style="">
              <a class="dropdown-item waves-effect" onclick="FormEditUser(1)" data-id="${id}" href="javascript:void(0);">
                <i class="mdi mdi-pencil-outline me-2"></i> แก้ไข
              </a>
              <a class="dropdown-item waves-effect" onclick="DeleteAction(this.dataset.id)" data-id="${id}" href="javascript:void(0);">
                <i class="mdi mdi-trash-can-outline me-2"></i> ลบ
              </a>
            </div>
          </div>
        </div>`

        return html;
      }

      function ToggleForm(action=false) {
        document.querySelectorAll('.form-add-new').forEach(element => {
          if(action){
            element.classList.remove('d-none');
          }else{
            element.classList.add('d-none');
          }
        })
      }

      function CancelForm(element){

        let id = element.dataset.id?element.dataset.id:'';
        access_level = element.dataset.access_level?element.dataset.access_level:false;
        let is_active = element.dataset.is_active?element.dataset.is_active:false;
        let btn_action =element.dataset.btn_action?element.dataset.btn_action:false;

        if(access_level) {
          let result = HTML_Access_Level({
            id: id,
            access_level: access_level
          })
          element.innerHTML = result;
        }

        if(is_active) {
          let result = HTML_IS_Active({
            id: id,
            is_active: is_active
          })
          element.innerHTML = result;
        }

        if(btn_action) {
          let result = HTML_BTN_Action({
            id: id,
            access_level: access_level
          })
          element.innerHTML = result;
        }
      }

      function SubmitEdit(btn){
        console.log(btn)
        let id = btn.dataset.id,
            action = btn.dataset.action,
            className = `.row_id-${id}`,
            access_level;

        //cancel action
        if(action == 'cancel'){
          document.querySelectorAll(className).forEach(CancelForm)
          return;
        }

        Swal.fire({
          icon: 'question',
          html: `คุณยืนยันที่จะบันทึกหรือไม่ ?`,
          showCancelButton: true,
        }).then(swal_res => {
          if (!swal_res.isConfirmed) {
            return false;
          }

          showLoading();
          //save action
          let select_edit = `.edit-data-${id}`;
          let form = new FormData();
          form.set('id', id);
          form.set('action', action);

          document.querySelectorAll(select_edit).forEach((element, index) => {
            //console.log(element)
            let name = element.name;
            let value = element.value;
            form.set(name, value);
          })

          let res = editUser(form);
          res.then(result => {
            console.log(result)
            hideLoading();
            if(result.status != 200){
              Swal.fire({
                icon: 'error',
                html: result.message
              })
              return;
            }

            //on success
            Swal.fire({
              icon: 'success',
              html: 'บันทึกสำเร็จ'
            }).then(swal_res => {
              initDataTable();
            })
            
          })
          .catch(err => {
            hideLoading();
            console.error(err)
            Swal.fire({
              icon: 'error',
              html: 'มีบางอย่างผิดพลาดโปรดลองใหม่อีกครั้ง'
            })
          })
          
        })

      }

      function FormEditUser(action=""){

        //loop until event.target == a
        let target = event.target;
        while(target.tagName != 'A' && target.tagName != 'DIV'){
          target = target.parentElement;
        }

        let id = target.dataset.id;
        //bootstrap5 button primary and danger
        let btn_action_save = `
          <div class="btn-group " role="group" aria-label="Basic example">
            <button type="button" onclick="SubmitEdit(this)"; data-action="save" data-id="${id}" class="btn btn-primary"  href="javascript:void(0);">
              <i class="mdi mdi-checkbox-marked-outline me-1"></i> 
              บันทึก
            </button>
            <button type="button" onclick="SubmitEdit(this)"; data-action="cancel" data-id="${id}" class="btn btn-secondary"  href="javascript:void(0);">
              <i class="mdi mdi-close-box-outline me-1"></i> 
              ยกเลิก
            </button>
          </div>`;

        if(action == ''){
          return;
        }

        // 1 == edit action
        if(action == '1') {

          //bootstrap5 selection access_level
          document.querySelectorAll(`.row_id-${id}`).forEach(element => {
            //console.log(element)
            let access_level = element.dataset.access_level?element.dataset.access_level:'';
            let btn_action = element.dataset.btn_action?element.dataset.btn_action:'';

            if(!access_level ){

              if(!btn_action){
                return;
              }

              element.innerHTML = btn_action_save;

              return;
            }
            
            let html = `
              <div class="form-floating form-floating-outline mb-4">
                <select required class="form-select edit-data-${id}" name="access_level" id="edit_access_level_${id}" aria-label="ตำแหน่ง">
                  <option disabled hidden selected value="">เลือกตำแหน่ง</option>
                  <option value="100" ${access_level==100?'selected':''}>Admin</option>
                  <option value="10" ${access_level==10?'selected':''}>User</option>
                </select>
                <label for="edit_access_level_${id}">ตำแหน่ง</label>
              </div>
            `;

            element.innerHTML = html;
          })

          //bootstrap5 selection is_active
          document.querySelectorAll(`.row_id-${id}`).forEach(element => {
            console.log(element)
            let is_active = element.dataset.is_active?element.dataset.is_active:'';
            let btn_action = element.dataset.btn_action?element.dataset.btn_action:'';
            
            if(!is_active ){

              if(!btn_action){
                return;
              }

              element.innerHTML = btn_action_save;

              return;
            }

            //bootstrap5 selection is_active
            let html = `
              <div class="form-floating form-floating-outline mb-4">
                <select required class="form-select edit-data-${id}" name="is_active" id="edit_is_active_${id}" aria-label="สถานะ">
                  <option disabled hidden selected value="">เลือกสถานะ</option>
                  <option value="1" ${is_active==1?'selected':''}>Active</option>
                  <option value="0" ${is_active==0?'selected':''}>Inactive</option>
                </select>
                <label for="edit_is_active_${id}">สถานะ</label>
              </div>
            `;

            element.innerHTML = html;
            
          })
        }

      }

      function ResetContentModal(){

        document.getElementById("modal-body-add-new-user").innerHTML = `<div class="row form-add-new">
                <div class="col-6 mb-4 mt-2">
                  <div class="form-floating form-floating-outline">
                    <input required type="text" id="nameBasic" class="form-control" name="form_fname" placeholder="ชื่อจริง">
                    <label for="nameBasic">ชื่อจริง</label>
                  </div>
                </div>
                <div class="col-6 mb-4 mt-2">
                  <div class="form-floating form-floating-outline">
                    <input required type="text" id="emailBasic" class="form-control" name="form_lname" placeholder="นามสกุล">
                    <label for="emailBasic">นามสกุล</label>
                  </div>
                </div>
              </div>
              <div class="row g-2 form-add-new">
                <div class="col mb-2">
                  <div class="form-floating form-floating-outline mb-4">
                    <select required class="form-select" name="access_level" id="exampleFormControlSelect1" aria-label="ตำแหน่ง">
                      <option disabled hidden selected value="">เลือกตำแหน่ง</option>
                      <option value="100">Admin</option>
                      <option value="10">User</option>
                    </select>
                    <label for="exampleFormControlSelect1">ตำแหน่ง</label>
                  </div>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-12" id="content_qr_login">
                  
                </div>
              </div>`;
      }

      function ResetForm(check=false){

        if(check) {
          Swal.fire({
            icon: 'question',
            html: `<p>ยืนยันการเพิ่ม User ใหม่เรียบร้อยแล้ว ?</p>`,
            showCancelButton: true,
          }).then(swal_res => {
            if (!swal_res.isConfirmed) {
              return false;
            }
            BasicModal.hide();
            initDataTable();
            clearInterval(interval_check);
            ResetContentModal();
            document.getElementById('cancel_qr_code').classList.add('d-none')
            document.getElementById("finished-btn").classList.add('d-none')
            document.getElementById('submit-btn').classList.remove('d-none')
          })
          return;
        }

        ResetContentModal();
        document.getElementById('cancel_qr_code').classList.add('d-none')
        document.getElementById("finished-btn").classList.add('d-none')
        document.getElementById('submit-btn').classList.remove('d-none')
      }


    </script>

    

  </body>
</html>
