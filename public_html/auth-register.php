<?php 

require_once __DIR__ . '/config.php';


require_once __DIR__ . "/middleware/index.php";

$SAVE_REGISTER = $APP_URL.$_ENV['SAVE_REGISTER'];
$URL_LOGIN_WEB = $_ENV['URL_LOGIN_WEB'];
$VERIFY_PATH = $_ENV['VERIFY_PATH'];


if(isset($UserData[0]['cid']) && !empty($UserData[0]['cid'])){
  header('Location: '. $APP_URL);
  exit;
}

?>
<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Register Basic - Pages </title>

    <meta name="description" content="" />

    <?php require_once __DIR__ . '/require_page/header.php'; ?>

    <!-- Page -->
    <link rel="stylesheet" href="<?php echo ASSET_URL('/vendor/css/pages/page-auth.css'); ?>" />

    <?php require_once __DIR__ . '/require_page/sub_header.php'; ?>

  </head>

  <body>
    <!-- Content -->

    <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Register Card -->
          <div class="card p-2">
            <!-- Logo -->
            <div class="app-brand justify-content-center mt-5">
              <a href="index.html" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  <span style="color: var(--bs-primary)">
                    <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z"
                        fill="currentColor" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z"
                        fill="black" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0 65.2656L60.4839 99.0795V119.859L0 65.2656Z"
                        fill="black" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z"
                        fill="currentColor" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M250 65.2656L189.516 99.8897V135.006L250 65.2656Z"
                        fill="black" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M250 65.2656L189.516 99.0497V120.886L250 65.2656Z"
                        fill="black" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                        fill="currentColor" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                        fill="white"
                        fill-opacity="0.15" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                        fill="currentColor" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                        fill="white"
                        fill-opacity="0.3" />
                    </svg>
                  </span>
                </span>
                <span class="app-brand-text demo text-heading fw-semibold">Materio</span>
              </a>
            </div>
            <!-- /Logo -->
            <div class="card-body mt-2">
              <h4 class="mb-2">Adventure starts here 🚀</h4>
              <p class="mb-4">Make your app management easy and fun!</p>

              <form id="formAuthentication" class="mb-3" action="index.html">
                <div class="d-flex justify-content-center">
                  <img src="<?php echo $result_jwt->picture; ?>" alt="user-avatar" class="d-block rounded my-3" height="100" width="100" id="uploadedAvatar">
                </div>
                <div class="form-floating form-floating-outline mb-3">
                  <input
                    type="text"
                    class="form-control bg-white"
                    id="LINE"
                    name="LINE"
                    placeholder="Enter your LINE"
                    value="<?php echo $result_jwt->name; ?>"
                    disabled
                    autofocus />
                  <label for="LINE">LINE</label>
                </div>
                <div class="form-floating form-floating-outline mb-3">
                  <input type="text" class="form-control" id="cid" name="cid" placeholder="โปรดใส่เลขบัตรประชาชน" />
                  <label for="เลขบัตรประชาชน">เลขบัตรประชาชน</label>
                </div>

                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                    <label class="form-check-label" for="terms-conditions">
                      I agree to
                      <a href="javascript:void(0);">privacy policy & terms</a>
                    </label>
                  </div>
                </div>
                <button class="btn btn-primary d-grid w-100">Sign up</button>
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="auth-login-basic.html">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
          <img
            src="<?php echo ASSET_URL('/img/illustrations/tree-3.png') ?>"
            alt="auth-tree"
            class="authentication-image-object-left d-none d-lg-block" />
          <img
            src="<?php echo ASSET_URL('/img/illustrations/auth-basic-mask-light.png') ?>"
            class="authentication-image d-none d-lg-block"
            alt="triangle-bg"
            data-app-light-img="illustrations/auth-basic-mask-light.png"
            data-app-dark-img="illustrations/auth-basic-mask-dark.png" />
          <img
            src="<?php echo ASSET_URL('/img/illustrations/tree.png') ?>"
            alt="auth-tree"
            class="authentication-image-object-right d-none d-lg-block" />
        </div>
      </div>
    </div>

    <!-- / Content -->

    <?php require_once __DIR__ . '/require_page/footer.php'; ?>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>


    <script>
      const cid_input = new Cleave('#cid', {
        blocks: [1, 4, 5, 2, 1],
        numericOnly: true,
      });
      //event on domloaded
      document.addEventListener("DOMContentLoaded", function (event) {
        
        document.getElementById('formAuthentication').addEventListener("submit", (event) => {
          event.preventDefault();
          Swal.fire({
            icon:'question',
            html: `<p>คุณต้องการบันทึกข้อมูลใช่หรือไม่</p>`,
            showCancelButton: true
          }).then((swal_res) => {

            if(!swal_res.isConfirmed){
              return;
            }

            let form = new FormData(event.target);
            form.set("cid", cid_input.getRawValue());
            let fetchOptions = {
              method: "POST",
              body: form,
            };

            let res = fetch("<?php echo htmlspecialchars($SAVE_REGISTER); ?>", fetchOptions)
            res.then(result => res.json())
            res.then((result) => {
              if (result.status == 200) {
                Swal.fire({
                  icon:'success',
                  html: `<p>บันทึกเรียบร้อย</p>`
                }).then((swal_result) => {
                  window.location.href = "/";
                })
                return;
              } 

              Swal.fire({
                icon:'warning',
                html: `
                  <p>บันทึกไม่สำเร็จ</p>
                  <p>${result.message}</p>
                `
              })
              
            })
            .catch((err) => {
              console.error(err);
              Swal.fire({
                icon:'error',
                html: 'มีบางอย่างผิดพลาดโปรดลองใหม่อีกครั้ง'
              })
            });

          })//end swal accept

        })//end form submit
          

      });
    </script>

  </body>
</html>
