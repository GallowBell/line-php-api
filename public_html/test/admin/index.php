<?php 
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <title>Admin</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 my-3">
                <a href="../main.php" class="btn btn-primary">ไปหน้าหลัก</a>
            </div>
        
            <h1>Admin Page</h1>

            <div class="col-12 my-3">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-action="AddNewDevice" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Add New Device
                </button>
                <button class="btn btn-primary" onclick="ShowHospcode()">
                    รายชื่อสาขาที่มีใน DB
                </button>
            </div>
            <hr>
            <div class="col-12 ">
                <!-- Table -->
                <table id="table" class="table mt-4">
                    <thead>
                        <tr>
                            <th class="text-center" title="Device ID">Device ID</th>
                            <th class="text-center" title="MAC Address">MAC Address</th>
                            <th class="text-center" title="Hospcode">Hospcode</th>
                            <th class="text-center" title="สาขา">สาขา</th>
                            <th class="text-center" title="Number">Number</th>
                            <th class="text-center" title="Action">Action</th>
                        </tr>
                    </thead>
                    <tbody>                       
                        <!-- Loop through data and populate table rows -->
                        <tr>
                            <td colspan="6" class="text-center">
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form id="form-add">
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Manage Device</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Device ID</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">MAC Address</label>
                            <input type="text" class="form-control" id="mac_address" name="mac_address">
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Hospcode</label>
                            <input type="text" class="form-control" id="hospcode" name="hospcode">
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Number</label>
                            <input type="text" class="form-control" id="number" name="number">
                        </div>
                        <div>
                            <input type="hidden" name="id" id="id" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btn-submit" data-action="" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        const dns = '<?php echo $_ENV['APP_URL']; ?>/api.php?action=';
        setTimeout(() => {
            let table = initTable();
        }, 500);

        const check_mark_icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
            <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
            <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
        </svg>`
        const x_mark_icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
        </svg>`
        const btn_submit = document.getElementById('btn-submit');
        const modal = new bootstrap.Modal(document.getElementById('exampleModal'), {
            keyboard: false
        })

        document.addEventListener('DOMContentLoaded', (event) => {
            console.log('DOM fully loaded and parsed');

            document.getElementById('exampleModal').addEventListener('show.bs.modal', (event) => {
                console.log(event)
                let btn = event.relatedTarget
                let action = btn.getAttribute('data-action')
                console.log(action)
                btn_submit.setAttribute('data-action', action)

                if(action == 'AddNewDevice'){
                    document.getElementById('name').value = "";
                    document.getElementById('mac_address').value = "";
                    document.getElementById('hospcode').value = "";
                    document.getElementById('number').value = "";
                    return;
                }

                if(action == 'EditDevice'){
                    console.log(btn.dataset)
                    document.getElementById('id').value = btn.getAttribute("data-id");
                    document.getElementById('name').value = btn.getAttribute("data-device-id")?btn.getAttribute("data-device-id"):"";
                    document.getElementById('mac_address').value = btn.getAttribute("data-mac_address")?btn.getAttribute("data-mac_address"):"";
                    document.getElementById('hospcode').value = btn.getAttribute("data-hospcode")?btn.getAttribute("data-hospcode"):"";
                    document.getElementById('number').value = btn.getAttribute("data-number")?btn.getAttribute("data-number"):"";
                    return;
                }

            })
        });

        document.getElementById('form-add').addEventListener('submit', async (event) => {
            event.preventDefault();

            let res = await Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save this data?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            })

            if(!res.isConfirmed){
                return;
            }

            let formdata = new FormData(event.target);
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            }
            let endpoint = btn_submit.getAttribute('data-action');
            console.log(endpoint)
            fetch(`${dns}${endpoint}`, requestOptions)
            .then(response => response.json())
            .then(result => {
                if(result.status !== 200){
                    alert(result.message?result.message:"Error");
                    return;
                }

                initTable();
                modal.hide();
            })
            .catch(error => {
                console.error('error', error)
            });
        })
        
        function initTable() {
            // Destroy the existing table
            if ($.fn.DataTable.isDataTable('#table')) {
                $('#table').DataTable().destroy();
            }

            let table = new DataTable('#table', {
                processing: true,
                ajax: {
                    url: dns+'getDeviceList',
                    method: 'POST'
                },
                dom: `
                <"row" r
                    <"col-12 mb-3 py-1"B>
                    <"col-sm-12 col-md-6 my-1 d-flex justify-content-start"l>
                    <"col-sm-12 col-md-6 my-1 d-flex justify-content-end"f>
                    <"col-12 row"
                        <" my-2 text-center" t>
                        <"col-sm-12 col-md-6 mb-3 d-flex justify-content-start"i>
                        <"col-sm-12 col-md-6 mb-3 d-flex justify-content-end"p>
                    >
                >`,
                columns: [
                    {
                        data:'device_id',
                        render: (data, type, row) => {
                            return `${data?data:'-'}`;
                        }
                    },
                    {
                        data:'mac_address',
                        render: (data, type, row) => {
                            return `${data?data:'-'}`;
                        }
                    },
                    {
                        data:'hospcode',
                        render: (data, type, row) => {
                            return `${data?data:'-'}`;
                        }
                    },
                    {
                        data:'name',
                        render: (data, type, row) => {
                            return `${data?data:'-'}`;
                        }
                    },
                    {
                        data:'number',
                        render: (data, type, row) => {
                            return `${data?data:'-'}`;
                        }
                    },
                    {
                        data:'id',
                        render: (data, type, row) => {
                            let is_active = row.is_active==1;
                            let html = `
                                <button 
                                    data-action="EditDevice"
                                    data-is_active="${is_active}" 
                                    data-id="${data?data:''}"
                                    data-device-id="${row.device_id?row.device_id:''}" 
                                    data-mac_address="${row.mac_address?row.mac_address:''}"
                                    data-hospcode="${row.hospcode?row.hospcode:''}"
                                    data-number="${row.number?row.number:''}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#exampleModal"
                                    class="btn btn-success"
                                    >
                                        Edit
                                </button>
                                <button 
                                    data-is_active="${is_active}" 
                                    onclick="ChangeStatus('${data}', ${!is_active})" 
                                    data-device-id="${data}" 
                                    class="btn btn-${is_active?"danger": "primary"}"
                                    >
                                        ${is_active?`${x_mark_icon} Disable`:`${check_mark_icon} Enable`}
                                </button>
                            `
                            return `${html}`;
                        }
                    },
                ]
            });

            return table;
        }

        async function ChangeStatus(id, is_active) {

            let formdata = new FormData();
            formdata.set('id', id);
            formdata.set('is_active', is_active);

            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            }
            let res = await fetch(`${dns}changeStatus`, requestOptions)
            let result = await res.json()
            if(result.status !== 200){
                alert(result.message?result.message:"Error");
                return;
            }
            initTable();
            return true;
        }

        async function EditDevice(formdata){

            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            }
            let res = await fetch(`${dns}editDevice`, requestOptions)
            let result = await res.json()
            if(result.status !== 200){
                alert(result.message?result.message:"Error");
                return;
            }
            initTable();
            return true;
        }

        function ShowHospcode(){
            let formdata = new FormData();
            let requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'manual'
            }
            fetch(`${dns}getHospcode`, requestOptions)
            .then(response => response.json())
            .then(result => {
                if(result.status !== 200){
                    alert(result.message?result.message:"Error");
                    return;
                }

                let html = `<div class="col-12">`;
                result.data.forEach(element => {
                    html += `<li>${element.hospcode} - ${element.name}</li>`
                });
                html += `</div>`;
                Swal.fire({
                    title: 'รายชื่อสาขาที่มีใน DB',
                    html: html,
                    icon: 'info',
                    confirmButtonText: 'OK'
                })
            })
            .catch(error => {
                console.error('error', error)
            });
        }
    </script>
</body>
</html>

