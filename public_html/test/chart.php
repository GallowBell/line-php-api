<?php 

    require_once __DIR__ . '/config.php';

    $hospcode = $_GET['hospcode'] ?? false;
    $number = $_GET['number'] ?? false;

    if(!$hospcode){
        header('Location: main.php');
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Temp <?php echo $hospcode; ?></title>
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
            <div class="col-12">
                <h1>Chart Temp <?php echo $hospcode; ?></h1>
                <form id="form-date">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-6">
                            <select name="number" style="height: 51px;" id="number" class="form-control">
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-6">
                            <div class="input-group mb-3">
                                <input type="text" id="date" class="form-control" placeholder="เลือกวันที่">
                                <div class="input-group-text">
                                    <select class="form-select" id="filter" name="filter">
                                        <option value="daily">รายงานรายวัน</option>
                                        <option value="hours" selected>ทุกชั่วโมง</option>
                                        <option value="30minutes">ทุก30นาที</option>
                                        <option value="15minutes">ทุก15นาที</option>
                                        <option value="none"  >ทุก1นาที</option>
                                    </select>
                                </div>
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 p-1 border mt-3 rounded">
                <div style="width: 100%;height:100%" class="px-3">
                    <canvas id="myChart" style="width:100%;max-width:100%"></canvas>
                </div>
            </div>
            <div class="col-12 mt-3 d-flex justify-content-start">
                <a href="main.php" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        const dns = '<?php echo $_ENV['APP_URL']; ?>/api.php?action=';

        var data = [];
        var labels = [];
        var myChart;
        let number;
        getDeviceListByHosname()
        .then(result => {
            let number = '<?php echo $number; ?>';
            result.data.forEach(element => {
                document.getElementById('number').innerHTML += `<option ${number==element.number?'selected':''} value="${element.number}">${element.device_id}</option>`;
            })
            RenderChart();
        }).catch(error => {
            console.log('error', error)
        });

        document.addEventListener('DOMContentLoaded', function() {

            $('#date').daterangepicker({
                timePicker: true,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss'
                }
            })

            document.getElementById('form-date').addEventListener('submit', (event) => {
                event.preventDefault();
                RenderChart();
            })

        });

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

        async function RenderChart(){
            let res = getData()
            showLoading();
            let date = document.getElementById('date').value;
            let date_start = (new Date(date.split(' - ')[0])).toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });
            let date_end = (new Date(date.split(' - ')[1])).toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });

            let prom = new Promise((resolve, reject) => {
                res.then(result => {
                    
                    hideLoading();

                    if(result.status != 200) {
                       let lastest = result.lastest?result.lastest:false;
                        if(!lastest){
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่พบข้อมูล',
                                text: 'โปรดตรวจสอบอุปกรณ์ หรือ วันที่ที่เลือก',
                            })
                            return;
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่พบข้อมูลตามวันที่ ที่กำหนด',
                            text: 'ข้อมูลล่าสุดวันที่ '+convertTime(lastest),
                        })
                        return;
                    }
                    //clear data
                    data = [];
                    labels = [];
                    if(result.data.length == 0){
                        //alert('ไม่พบข้อมูล')
                        let lastest = result.lastest?result.lastest:false;
                        if(!lastest){
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่พบข้อมูล',
                                text: 'โปรดตรวจสอบอุปกรณ์ หรือ วันที่ที่เลือก',
                            })
                            return;
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่พบข้อมูลตามวันที่ ที่กำหนด',
                            text: 'ข้อมูลล่าสุดวันที่'+convertTime(lastest),
                        })
                        return;
                    }
                    const label_title = result.data[0].device_id?result.data[0].device_id:''
                    result.data.forEach(element => {
                        data.push(element.temp);
                        labels.push(convertTime(element.date_time));
                    });
                    var ctx = document.getElementById('myChart').getContext('2d');
                    if (myChart) {
                        myChart.destroy();
                    }

                    let filter_select = document.getElementById('filter').value;
                   
                    let backgroundColor = filter_select=='daily'?[
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(132, 99, 255, 0.8)'
                    ]:['rgba(111, 0, 111, 0.3)'];

                    myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            // labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                            labels: labels,
                            datasets: [{
                                label: 'อุณหภูมิ',
                                data: data,
                                pointRadius: filter_select=='none'?1:3,
                                backgroundColor:backgroundColor,
                                borderColor: backgroundColor,
                                borderWidth: 1,
                                tension: 0.4
                            }],
                            parsing: false
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    type: 'linear',
                                    min: result.min-0.2,
                                    max: result.max+0.2
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: `กราฟแสดงอุณหภูมิ ${date_start} ถึง ${date_end} เครื่อง ${label_title}`
                                }
                            }
                        }
                    });
                    
                    resolve(true)
                //console.log(labels, data)
                }).catch(error => {
                    hideLoading();
                    console.error('error', error)
                    reject(error)
                });
            });

            let result = await prom;
            return result;
            
        }

        async function getData(){
            let formdata = new FormData();
            let date = document.getElementById('date').value;
            let number = document.getElementById('number').value;
            let filter = document.getElementById('filter').value?document.getElementById('filter').value:'';
            if(filter == 'none'){
                setTimeout(() => {
                    hideLoading();
                }, 200);
                let swal_res = await Swal.fire({
                    icon: 'warning',
                    title: 'คุณยืนยันที่จะดูข้อมูลนี้หรือไม่ ?',
                    text: 'คุณกำลังเลือกข้อมูลที่มีความละเอียดมากอาจส่งผลต่อ Performance ของ Browser',
                    showCancelButton: true,
                })
                if(!swal_res.isConfirmed){
                    hideLoading();
                    return;
                }
            }
            let date_start = date.split(' - ')[0];
            let date_end = date.split(' - ')[1];
            formdata.append("date_start", date_start);
            formdata.append("date_end", date_end);
            formdata.append("hospcode", '<?php echo $hospcode; ?>');
            formdata.append("number", number);
            formdata.append("filter", filter);
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            };
            let res = await fetch(`${dns}getTempByDate`, requestOptions)
            let result = await res.json()
            return result;
            
        }

        async function showLoading() {
            // Show loading spinner
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'flex';
            let prom = new Promise((resolve, reject) => {
                let interval = setInterval(() => {
                    if(document.getElementById('loading').style.display == 'flex'){
                        resolve(true)
                        interval = null;
                        return;
                    } 
                }, 500);
            });
            let result = await prom;
            return result;
        }

        async function hideLoading() {
            // Hide loading spinner
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'none';
            let prom = new Promise((resolve, reject) => {
                let interval = setInterval(() => {
                    if(document.getElementById('loading').style.display == 'none'){
                        resolve(true)
                        interval = null;
                        return;
                    } 
                }, 500);
            });
            let result = await prom;
            return result;
        }

        async function getDeviceListByHosname(){
            let formdata = new FormData();
            formdata.set("hospcode", '<?php echo $hospcode; ?>');
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            };
            let res = await fetch(`${dns}getDeviceByHosname`, requestOptions)
            let result = await res.json()
            console.log(result)
            return result;
        }

    </script>
</body>
</html>