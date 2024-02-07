<?php 

    require_once __DIR__ . '/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body>

    <div id="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 my-2">
                <h1>รายชื่อสาขา</h1>
                <select name="hospcode" id="hospcode" class="form-control">
                    <option selected hidden disabled>เลือกสาขา</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-8 d-flex justify-content-center mt-3" style="align-content: center;flex-wrap: wrap;">
                <h4 id="time_auto">
                    
                </h4>
            </div>
            <div class="col-12">
                <div class="row" id="card-container">
                    <p class="placeholder-glow">
                        <span class="placeholder col-12"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        const dns = '<?php echo $_ENV['APP_URL']; ?>/api.php?action=';
        const time_interval = 60000;
        autoTime();
        document.addEventListener('DOMContentLoaded', () => {

            let time_auto = document.getElementById('time_auto');

            setInterval(autoTime, 1000);

            let getAll = getLastTempAll();
            getAll.then(result => {
                console.log(result)
                let card = document.getElementById('card-container');
                card.innerHTML = "";
                Object.keys(result.data).forEach(key => {
                    let element = result.data[key];
                    console.log('element obj', element)
                    card.innerHTML += `<h1 class="mt-3">${element[0].name?element[0].name:key}</h1><hr>`;
                    element.forEach(data => {
                        card.innerHTML += RenderCard(data);
                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                    })
                    //card.innerHTML += RenderCard(element);
                });
            })
            .catch(err => {
                console.error(err)
            })

            let res = getHospcode();
            res.then(result => {
                console.log(result)
                document.getElementById('hospcode').innerHTML = '<option selected value="-1" >เลือกทั้งหมด</option>';
                result.data.forEach(element => {
                    console.log(element)
                    document.getElementById('hospcode').innerHTML += `<option value="${element.namehos}">${element.name}</option>`;
                });
                return true;
            })
            .then(result => {
                setInterval(() => {
                    InitTemp();
                    console.log('first')
                }, time_interval);
            })
            .catch(err => {
                console.error(err)
            })

            //on change
            document.getElementById('hospcode').addEventListener('change', InitTemp)
        })

        function InitTemp() {
            showLoading();
            let value = document.getElementById('hospcode').value;
            let formdata = new FormData();
            if(value == -1){
                let getAll = getLastTempAll();
                getAll.then(result => {
                    hideLoading();
                    console.log(result)
                    let card = document.getElementById('card-container');
                    card.innerHTML = "";
                    //javascript loop through object
                    Object.keys(result.data).forEach(key => {
                        let element = result.data[key];
                        card.innerHTML += `<h1 class="mt-3">${element[0].name?element[0].name:key}</h1><hr>`;
                        element.forEach(data => {
                            card.innerHTML += RenderCard(data);
                            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                        })
                        //card.innerHTML += RenderCard(element);
                    });

                })
                .catch(err => {

                    hideLoading();
                    console.error(err)
                })
                return;
            }

            formdata.append('hospcode', value);
            let res = getLastTemp(formdata);
            res.then(result => {
                hideLoading();
                console.log('result asd', result)
                let card =  document.getElementById('card-container');
                card.innerHTML = "";
                card.innerHTML += `<h1 class="mt-3">${result.data[0].name?result.data[0].name:'key'}</h1><hr>`;
                result.data.forEach(element => {
                    card.innerHTML += RenderCard(element);
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                });
            }).catch(err => {
                hideLoading();
                console.error(err)
            })
        }

        function convertTime(time){
            let date = new Date(time);
            let result = date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });
            return result;
        }
        
        let interval;
        function RenderCard(params){

            let number = params?params.number:"";
            let device_id = params?params.device_id:"";
            let temp = params?params.temp:"";
            let hospcode = params?params.hospcode:"";
            let date_time = params?params.date_time:"";
            let relativeTime = timeAgo(new Date(date_time));
            if(interval){
                clearInterval(interval);
            }
            interval = setInterval(() => {
                relativeTime = timeAgo(new Date(date_time));
                document.getElementById(`relativeTime-${number}`).innerHTML = relativeTime;
            }, 1000);
            let dt_TH = convertTime(date_time);
            let html = `
            <div class="card col-sm-12 col-md-6 col-lg-4 col-xl-3 m-2" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">
                        เครื่อง ${device_id}
                    </h5>
                    <div class="row">
                        <div class="col-12 text-center my-3">
                            <h1>
                                <span style="font-weight:solid">${temp} °C</span>
                            </h1>
                        </div>
                        <div class="col-12">
                            <p 
                                style="cursor: pointer;"
                                class="text-muted" 
                                data-bs-placement="bottom"
                                data-bs-toggle="tooltip"  
                                data-bs-title="${dt_TH}">
                                    ข้อมูลล่าสุด 
                                    <span id="relativeTime-${number}">
                                        ${relativeTime}
                                    </span>
                            </p>
                        </div>
                    </div>
                    <a href="chart.php?hospcode=${hospcode}&number=${number}" class="btn btn-primary">ดูข้อมูล</a>
                </div>
            </div>`;
            return html;
        }

        async function getHospcode(){
            let formdata = new FormData();
            
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            };
            let res = await fetch(`${dns}getHospcode`, requestOptions)
            let result = await res.json()
            return result;
            
        }

        async function getLastTemp(formdata){

            /* let hospcode = 
            let number =  */
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            };
            let res = await fetch(`${dns}getLastTemp`, requestOptions)
            let result = await res.json()
            return result;
        }

        async function getLastTempAll(){
            let formdata = new FormData();
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            };
            let res = await fetch(`${dns}getLastTempAll`, requestOptions)
            let result = await res.json()
            return result;
        }

        async function showLoading() {
            // Show loading spinner
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'flex';
        }

        async function hideLoading() {
            // Hide loading spinner
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'none';
        }

        function timeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            let value = diffInSeconds;

            if (value < 60) {
                return `${value} วินาทีที่แล้ว`;
            }

            value = Math.floor(value / 60);
            if (value < 60) {
                return `${value} นาทีที่แล้ว`;
            }

            value = Math.floor(value / 60);
            if (value < 24) {
                return `${value} ชั่วโมงที่แล้ว`;
            }

            value = Math.floor(value / 24);
            if (value < 30) {
                return `${value} วันที่แล้ว`;
            }

            value = Math.floor(value / 30);
            if (value < 12) {
                return `${value} เดือนที่แล้ว`;
            }

            value = Math.floor(value / 12);
            return `${value} ปีที่แล้ว`;
        }

        function autoTime(){
            let date = new Date();
            let result = date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric'
            });
            time_auto.innerHTML = result;
        }
    </script>
</body>
</html>