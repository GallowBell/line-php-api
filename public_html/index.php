<?php 

require_once __DIR__ . "/config.php";

require_once __DIR__ . "/middleware/index.php";

$SAVE_REGISTER = $APP_URL.$_ENV['SAVE_REGISTER'];

//echo json_encode($UserData, JSON_PRETTY_PRINT);
$province = array(
  'กระบี่', 'กรุงเทพมหานคร', 'กาญจนบุรี', 'กาฬสินธุ์', 'กำแพงเพชร',
  'ขอนแก่น',
  'จันทบุรี',
  'ฉะเชิงเทรา',
  'ชลบุรี', 'ชัยนาท', 'ชัยภูมิ', 'ชุมพร', 'เชียงราย', 'เชียงใหม่',
  'ตรัง', 'ตราด', 'ตาก',
  'นครนายก', 'นครปฐม', 'นครพนม', 'นครราชสีมา', 'นครศรีธรรมราช', 'นครสวรรค์', 'นนทบุรี', 'นราธิวาส', 'น่าน',
  'บึงกาฬ', 'บุรีรัมย์',
  'ปทุมธานี', 'ประจวบคีรีขันธ์', 'ปราจีนบุรี', 'ปัตตานี',
  'พระนครศรีอยุธยา', 'พะเยา', 'พังงา', 'พัทลุง', 'พิจิตร', 'พิษณุโลก', 'เพชรบุรี', 'เพชรบูรณ์', 'แพร่',
  'ภูเก็ต',
  'มหาสารคาม', 'มุกดาหาร', 'แม่ฮ่องสอน',
  'ยโสธร', 'ยะลา',
  'ร้อยเอ็ด', 'ระนอง', 'ระยอง', 'ราชบุรี',
  'ลพบุรี', 'ลำปาง', 'ลำพูน', 'เลย',
  'ศรีสะเกษ',
  'สกลนคร', 'สงขลา', 'สตูล', 'สมุทรปราการ', 'สมุทรสงคราม', 'สมุทรสาคร', 'สระแก้ว', 'สระบุรี', 'สิงห์บุรี', 'สุโขทัย', 'สุพรรณบุรี', 'สุราษฎร์ธานี', 'สุรินทร์',
  'หนองคาย', 'หนองบัวลำภู',
  'อ่างทอง', 'อำนาจเจริญ', 'อุดรธานี', 'อุตรดิตถ์', 'อุทัยธานี', 'อุบลราชธานี'
);

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

    <title>Account settings - Account </title>

    <meta name="description" content="" />

    <?php require_once __DIR__ . '/require_page/header.php'; ?>
    <?php require_once __DIR__ . '/require_page/sub_header.php'; ?>

  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <!-- Layout container -->
        <div class="layout-page" style="padding-left: 0rem;">

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

              <div class="row">
                <div class="col-md-12">
                  <!-- <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2 gap-lg-0">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"
                        ><i class="mdi mdi-account-outline mdi-20px me-1"></i>Account</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-account-settings-notifications.html"
                        ><i class="mdi mdi-bell-outline mdi-20px me-1"></i>Notifications</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-account-settings-connections.html"
                        ><i class="mdi mdi-link mdi-20px me-1"></i>Connections</a
                      >
                    </li>
                  </ul> -->
                  <div class="card mb-4">
                    <h4 class="card-header bg-line">
                        <span class="mx-4"></span>LINE</span>
                    </h4>
                    <!-- Account -->
                    <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                          src="<?php echo $UserData[0]['pictureUrl'] ?>"
                          alt="user-avatar"
                          class="d-block w-px-120 h-px-120 rounded"
                          id="uploadedAvatar" />
                        <div class="button-wrapper">
                          <h5>ข้อมูลสมาชิก <?php echo $UserData[0]['displayName'] ?></h5>
                          <!-- <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="mdi mdi-tray-arrow-up d-block d-sm-none"></i>
                            <input
                              type="file"
                              id="upload"
                              class="account-file-input"
                              hidden
                              accept="image/png, image/jpeg" />
                          </label>
                          <button type="button" class="btn btn-outline-danger account-image-reset mb-3">
                            <i class="mdi mdi-reload d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>

                          <div class="text-muted small">Allowed JPG, GIF or PNG. Max size of 800K</div> -->
                        </div>
                      </div>
                    </div>
                    <div class="card-body pt-2 mt-1">
                      <form id="formAccountSettings" method="POST" onsubmit="return false">
                        <div class="row mt-2 gy-4">
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="cid"
                                name="cid"
                                placeholder="เลขบัตรประจำตัวประชาชน"
                                value="<?php echo $UserData[0]['cid'] ?>" />
                              <label for="cid">เลขบัตรประจำตัวประชาชน</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <input
                                class="form-control"
                                type="text"
                                id="firstName"
                                name="fname"
                                placeholder="ชื่อจริง"
                                value="<?php echo $UserData[0]['fname']?$UserData[0]['fname']:"" ?>"
                                autofocus />
                              <label for="firstName">ชื่อจริง</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <input 
                                class="form-control" 
                                type="text" 
                                name="lname" 
                                placeholder="นามสกุล" 
                                id="lastName" 
                                value="<?php echo $UserData[0]['lname']?$UserData[0]['lname']:"" ?>" />
                              <label for="lastName">นามสกุล</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <input
                                class="form-control"
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo $UserData[0]['email']?$UserData[0]['email']:"" ?>"
                                placeholder="E-mail" />
                              <label for="email">E-mail</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="input-group input-group-merge">
                              <span class="input-group-text">TH (+66)</span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="tel"
                                  name="tel"
                                  class="form-control"
                                  value="<?php echo $UserData[0]['tel']?$UserData[0]['tel']:"" ?>"
                                  placeholder="เบอร์โทรศัพท์" />
                                <label for="tel">เบอร์โทรศัพท์</label>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                              <textarea
                                type="text"
                                class="form-control h-px-100"
                                id="address"
                                name="address"
                                placeholder="ที่อยู่"><?php echo $UserData[0]['address']?$UserData[0]['address']:"" ?></textarea>
                              <label for="address">ที่อยู่</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <select id="province" name="province" class="select2 form-select">
                                <?php 

                                  $default_option="<option value='' disabled selected hidden>เลือกจังหวัด</option>";
                                  $selected = "";

                                  foreach ($province as $key => $value) {

                                    if($UserData[0]['province']==$value){
                                      $selected = $UserData[0]['province']==$value?" selected ":"";
                                      $default_option="";
                                      $province_result .= "<option ".$selected." value='".$value."'>".$value."</option>";
                                      continue;
                                    }

                                    $province_result .= "<option value='".$value."'>".$value."</option>";
                                  }

                                  echo $default_option.$province_result;
                                ?>
                              </select>
                              <label for="province">จังหวัด</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="zipCode"
                                name="zipcode"
                                placeholder="รหัสไปรษณี"
                                value="<?php echo $UserData[0]['zipcode']?$UserData[0]['zipcode']:"" ?>"
                                maxlength="6" />
                              <label for="zipCode">รหัสไปรษณี</label>
                            </div>
                          </div>
                        </div>
                        <div class="mt-4">
                          <button type="submit" class="btn btn-primary me-2">บันทึกข้อมูล</button>
                        </div>
                      </form>
                    </div>
                    <!-- /Account -->
                  </div>
                  <div class="card">
                    <h5 class="card-header fw-normal">Logout</h5>
                    <div class="card-body">
                      <!-- <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                          <h6 class="alert-heading mb-1">Are you sure you want to delete your account?</h6>
                          <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                      </div> -->
                      <form id="formAccountDeactivation" method="get" action="<?php echo htmlspecialchars($REVOKE_PATH); ?>">
                        <!-- <div class="form-check mb-3 ms-3">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            name="accountActivation"
                            id="accountActivation" />
                          <label class="form-check-label" for="accountActivation"
                            >I confirm my account deactivation</label
                          >
                        </div> -->
                        <button type="submit" class="btn btn-danger">Logout</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                  <div class="text-body mb-2 mb-md-0">
                    ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    , made with <span class="text-danger"><i class="tf-icons mdi mdi-heart"></i></span> by
                    <a href="https://themeselection.com" target="_blank" class="footer-link fw-medium"
                      >ThemeSelection</a
                    >
                  </div>
                  <div class="d-none d-lg-inline-block">
                    <a href="https://themeselection.com/license/" class="footer-link me-3" target="_blank">License</a>
                    <a href="https://themeselection.com/" target="_blank" class="footer-link me-3">More Themes</a>

                    <a
                      href="https://demos.themeselection.com/materio-bootstrap-html-admin-template/documentation/"
                      target="_blank"
                      class="footer-link me-3"
                      >Documentation</a
                    >

                    <a
                      href="https://github.com/themeselection/materio-bootstrap-html-admin-template-free/issues"
                      target="_blank"
                      class="footer-link"
                      >Support</a
                    >
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

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

    <?php require_once __DIR__ . '/require_page/footer.php'; ?>

    <!-- Page JS -->
    <!-- <script src="<?php echo ASSET_URL('/js/pages-account-settings-account.js') ?>"></script> -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>



    <script>
      //vanila js document event dom ready
      const SAVE_REGISTER = "<?php echo htmlspecialchars($SAVE_REGISTER) ?>";
      const cid_input = new Cleave('#cid', {
        blocks: [1, 4, 5, 2, 1],
        numericOnly: true,
      });
      const tel_input = new Cleave('#tel', {
        blocks: [3, 3, 4],
        numericOnly: true,
        delimiter: '-'
      });

      document.addEventListener("DOMContentLoaded", () => {

      })

      document.getElementById("formAccountSettings").addEventListener("submit", (event) => {
        event.preventDefault();

        Swal.fire({
          icon:'question',
          html: `<p>คุณต้องการบันทึกข้อมูลใช่หรือไม่</p>`,
          showCancelButton: true
        }).then((swal_res) => {

          if(!swal_res.isConfirmed){
            return;
          }

          let form = new FormData(event.target)
          let cid = cid_input.getRawValue()
          let tel = tel_input.getRawValue()

          form.set('cid', cid);
          form.set('tel', tel);

          let fetch_save = SaveRegister(form)
          
          fetch_save.then((result) => {

            if (result.status == 200) {
              Swal.fire({
                icon:'success',
                html: `<p>บันทึกเรียบร้อย</p>`
              })
              return;
            }

            Swal.fire({
              icon:'warning',
              html: `
                <p>บันทึกไม่สำเร็จ</p>
                <p>${result.message?result.message:"มีบางอย่างผิดพลาด"}</p>
              `
            })
          }).catch(err => {
            console.error(err)
            Swal.fire({
              icon:'error',
              html: 'มีบางอย่างผิดพลาดโปรดลองใหม่อีกครั้ง'
            })
          }).finally(() => {
            console.log('finally')
          })

        })//end swal accept

      })// end formAccountSettings submit

      const SaveRegister = async (form) => {
        let response = await fetch(SAVE_REGISTER, {
          method: "POST",
          body: form
        })
        let result = await response.json()
        return result
      }

      function showLoadingSpinner() {
        // Create a new div for the spinner
        let spinnerDiv = document.createElement('div');
        spinnerDiv.setAttribute('id', 'spinnerDiv');
        spinnerDiv.style.position = 'fixed';
        spinnerDiv.style.top = '0';
        spinnerDiv.style.right = '0';
        spinnerDiv.style.bottom = '0';
        spinnerDiv.style.left = '0';
        spinnerDiv.style.display = 'flex';
        spinnerDiv.style.justifyContent = 'center';
        spinnerDiv.style.alignItems = 'center';
        spinnerDiv.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';  // semi-transparent background

        // Create the spinner
        let spinner = document.createElement('div');
        spinner.setAttribute('class', 'spinner-border text-light');
        spinner.style.width = '3rem';
        spinner.style.height = '3rem';

        // Append the spinner to the div
        spinnerDiv.appendChild(spinner);

        // Append the div to the body
        document.body.appendChild(spinnerDiv);
    }

      function removeLoadingSpinner() {
          let spinnerDiv = document.getElementById('spinnerDiv');
          if (spinnerDiv) {
              spinnerDiv.remove();
          }
      }

    </script>
  </body>
</html>
