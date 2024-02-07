let table_bot_title /* ,
DomBasicModal = document.getElementById('basicModal'),
BasicModal = new bootstrap.Modal(DomBasicModal, {
    keyboard: false
}) */;

//css transition delay
const set_css_transition = 350;
const input_type = document.getElementById("type");

const toastElList = new bootstrap.Toast("#basic-toast", {});

/* DomBasicModal.addEventListener('click', async () => {}) */

document.addEventListener("DOMContentLoaded", (event) => {
  initDataTable();

  document
    .getElementById("data_response")
    .addEventListener("input", (event) => {
      let input = event.target;
      let value = input.value;
      let is_json = isJson(value);
      let type = document.getElementById("type");
      let change_event = new Event("change");
      if (is_json) {
        type.value = "flex";
        input_type.dispatchEvent(change_event);
        return;
      }
      input_type.dispatchEvent(change_event);
      type.value = "text";
      return;
    });

  document.getElementById("is_use_time").addEventListener("change", (event) => {
    let checked = event.target.checked;
    let input_time_response = document.getElementById("input-time-response");
    let response_time_start = document.getElementById("response_time_start");
    let response_time_end = document.getElementById("response_time_end");

    if (checked) {
      input_time_response.classList.remove("d-none");
      response_time_start.removeAttribute("required");
      response_time_end.removeAttribute("required");
      return;
    }

    input_time_response.classList.add("d-none");

    response_time_start.value = "";
    response_time_end.value = "";
    response_time_start.setAttribute("required", true);
    response_time_end.setAttribute("required", true);
    return;
  });

  //set flatpickr
  flatpickr("#response_time_start", {
    onChange: function (selectedDates, dateStr, instance) {
      console.log(selectedDates, dateStr, instance);
    },
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    locale: "th",
    //defaultDate: data.start_time?data.start_time:''
  });
  flatpickr("#response_time_end", {
    onChange: function (selectedDates, dateStr, instance) {
      console.log(selectedDates, dateStr, instance);
    },
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    locale: "th",
    //defaultDate: data.end_time?data.end_time:''
  });

  $("#caption").on("select2:select", function (e) {
    //clear search value
    console.log(e);
    $(".select2-search__field").val("");

    /* let texts = $('#caption').val();

    let form = new FormData()

    texts.forEach((text, index) => {
        form.append('caption[]', text);
    })                

    let res = CheckCaptionDuplicate(form)
    res.then(result => {
        console.log(result)
        let added_label = $(`.select2-selection__choice[title="${text}"]`)[0]
        if(result.total > 0) {
            added_label.classList.add('bg-danger')
            added_label.classList.add('text-white')
            added_label.title="Caption นี้ซ้ำกับข้อความอื่นๆ"
            added_label.insertAdjacentHTML('beforeend', '<i class="pe-pointer mdi mdi-alert-circle-outline ms-2"></i>')
            return true;
        }

        return false;
    }).catch(err => {
        console.error(err)
    }).finally(() => {
        console.log('finally select2:select')
    }) */
  });
});

document
  .getElementById("save-bot-response")
  .addEventListener("submit", async (event) => {
    event.preventDefault();
    let target = event.target;
    let action = target.dataset.action;
    let content_table = document.getElementById("content-table");

    Swal.fire({
      icon: "question",
      title: "ยืนยันการบันทึกข้อมูล ?",
      text: "ข้อมูลที่คุณกรอกจะถูกบันทึกลงในระบบ",
      showCancelButton: true,
    })
      .then((result) => {
        if (!result.isConfirmed) {
          throw "cancel";
        }
      })
      .then(() => {
        if (action == "edit") {
          let result = ActionEditForm(event);
          let check_dnone = setInterval(() => {
            if (!content_table.classList.contains("d-none")) {
              clearInterval(check_dnone);
              initDataTable();
              return;
            }
          }, 250);

          return;
        }

        if (action == "add") {
          let result = ActionAddForm(event);
          let check_dnone = setInterval(() => {
            if (!content_table.classList.contains("d-none")) {
              clearInterval(check_dnone);
              initDataTable();
              return;
            }
          }, 250);
          return;
        }
      })
      .catch((err) => {
        console.error(err);
      });
  });

async function ActionAddForm(event) {
  let target = event.target;
  let action = target.dataset.action;
  let form = new FormData(event.target);
  let captions = $("#caption").val();
  if (captions.length <= 0) {
    Swal.fire({
      icon: "warning",
      title: "Warning",
      text: "กรุณาเลือกข้อความที่ตรวจจับ",
    });
    return await false;
  }

  captions.forEach((caption, index) => {
    form.append(`caption[${index}]`, caption);
  });

  let data = Object.fromEntries(form.entries());
  console.log(data);

  let result = await addBotResponse(form);
  console.log(result);
  if (result.status != 200) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่อีกครั้ง",
    });
    return await false;
  }

  Swal.fire({
    icon: "success",
    title: "Success",
    text: "บันทึกข้อมูลสำเร็จ",
  })
    .then(async (result) => {
      let res_close = await CloseDetail("finished");
      initDataTable();
    })
    .catch((err) => {
      console.error(err);
    });
}

async function ActionEditForm(event) {
  let target = event.target;
  let action = target.dataset.action;

  let form = new FormData(event.target);
  let id = event.submitter.dataset.id;

  form.set("id", id);

  let captions = $("#caption").val();
  if (captions.length <= 0) {
    Swal.fire({
      icon: "warning",
      title: "Warning",
      text: "กรุณาเลือกข้อความที่ตรวจจับ",
    });
    return;
  }

  captions.forEach((caption, index) => {
    form.append(`caption[${index}]`, caption);
  });

  /* let data = Object.fromEntries(form.entries());
console.log(data) */

  let result = await editBotResponse(form);

  if (result.status != 200) {
    swal.fire({
      icon: "error",
      title: "Error",
      text: "เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่อีกครั้ง",
    });
    console.error(result);
    return;
  }

  swal
    .fire({
      icon: "success",
      title: "Success",
      text: "บันทึกข้อมูลสำเร็จ",
    })
    .then((result) => {
      CloseDetail();
    })
    .catch((err) => {
      console.error(err);
    });
  return;
}

input_type.addEventListener("change", (event) => {
  console.log(event);
  let select = event.target;
  let value = select.value;
  let altText_input = document.getElementById("altText-input");
  if (value == "flex") {
    altText_input.classList.remove("d-none");
    altText_input.setAttribute("required", true);
    return;
  }
  altText_input.classList.add("d-none");
  altText_input.removeAttribute("required");
});

async function CloseDetail(action = false) {
  let prom = new Promise((resolve, reject) => {
    if (document.getElementById("save-bot-response").dataset.action == "add") {
      if (action == "finished") {
        resolve(true);
        return true;
      }

      Swal.fire({
        icon: "question",
        title: "ยืนยันที่จะย้อนกลับ ?",
        text: "ข้อมูลที่คุณกรอกจะหายไปทั้งหมด",
        showCancelButton: true,
      }).then((swal_resl) => {
        if (!swal_resl.isConfirmed) {
          resolve(false);
          return false;
        }
        resolve(true);
        return true;
      });
    } else {
      resolve(true);
      return true;
    }
  });

  let check_accept = await prom;
  if (!check_accept) {
    return false;
  }

  document.getElementById("content-table").classList.remove("hide");
  document.getElementById("content-setting-response").classList.add("hide");
  document.getElementById("event_type").options.selectedIndex = 0;

  let css_transition = new Promise((resolve) => {
    setTimeout(() => {
      document.getElementById("content-table").classList.remove("d-none");
      document
        .getElementById("content-setting-response")
        .classList.add("d-none");
      resolve(true);
    }, set_css_transition);
  });
  let result = await css_transition;
  return result;
}

async function ShowAddForm() {
  document.getElementById("content-table").classList.add("hide");
  document.getElementById("content-setting-response").classList.remove("hide");
  document.getElementById("save-bot-response").reset();
  $("#caption").val(null).trigger("change");
  document.getElementById("save-bot-response").dataset.action = "add";

  /* Set Value */
  flatpickr("#response_time_start", {
    onChange: function (selectedDates, dateStr, instance) {
      console.log(selectedDates, dateStr, instance);
    },
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    locale: "th",
  });
  flatpickr("#response_time_end", {
    onChange: function (selectedDates, dateStr, instance) {
      console.log(selectedDates, dateStr, instance);
    },
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    locale: "th",
  });

  $("#caption").select2({
    theme: "bootstrap-5",
    width: $(this).data("width")
      ? $(this).data("width")
      : $(this).hasClass("w-100")
      ? "100%"
      : "style",
    closeOnSelect: false,
    tags: true,
    createTag: (params) => {
      return {
        id: params.term,
        text: params.term,
        newOption: true,
      };
    },
    templateResult: (data, container) => {
      let is_regex;
      let check_is_regex = document.getElementById("is_regex").checked ? 1 : 0;

      if (data.element) {
        is_regex = data.element.dataset.is_regex
          ? data.element.dataset.is_regex
          : check_is_regex;
      }

      let $result = $("<div></div>");
      $result.html(`<div class="d-flex justify-content-between">
            <span>${data.text}</span>
            ${
              data.newOption
                ? `<span class="badge bg-label-success">NEW</span>`
                : ``
            }
        </div>`);

      return $result;
    },
  });
  let css_transition = new Promise((resolve) => {
    setTimeout(() => {
      hideLoading();
      document.getElementById("content-table").classList.add("d-none");
      document
        .getElementById("content-setting-response")
        .classList.remove("d-none");
      resolve(true);
    }, set_css_transition);
  });
  let result = await css_transition;
  return result;
}

async function ShowEditForm() {
  document.getElementById("content-table").classList.add("hide");
  document.getElementById("content-setting-response").classList.remove("hide");
  document.getElementById("save-bot-response").dataset.action = "edit";

  setTimeout(() => {
    hideLoading();
    document.getElementById("content-table").classList.add("d-none");
    document
      .getElementById("content-setting-response")
      .classList.remove("d-none");
  }, set_css_transition);

  let A = event.target ? event.target : false;
  let ID = A.dataset.id ? A.dataset.id : false;

  console.log(ID);

  let filtered = filterDataFromDataTable("id", ID);

  if (filtered.length == 0) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "ไม่พบข้อมูล โปรดรีเฟรชแล้วลองใหม่อีกครั้ง",
    });
    return;
  }

  let data = filtered[0];
  console.log("data", data);
  document.getElementById("btn-save-bot-edit").dataset.id = ID;
  document.getElementById("test-sent-message").dataset.id = ID;
  document.getElementById("btn-delete-close").dataset.id = ID;

  let relativeTime = moment(data.last_update, "YYYY-MM-DD HH:mm:ss").fromNow();
  let DateTimeTH = moment(data.last_update).format("LLLL");

  let formData = new FormData();
  formData.set("id", ID);

  let res = await getBotCaption(formData);

  console.log("data.start_time", data.start_time);
  console.log("data.end_time", data.end_time);
  /* Set Value */

  let input_time_response = document.getElementById("input-time-response");
  let response_time_start = document.getElementById("response_time_start");
  let response_time_end = document.getElementById("response_time_end");

  if (data.is_use_time) {
    document.getElementById("is_use_time").checked = true;
    input_time_response.classList.remove("d-none");
    response_time_start.value = data.start_time ? data.start_time : "";
    response_time_end.value = data.end_time ? data.end_time : "";
    response_time_start.setAttribute("required", true);
    response_time_end.setAttribute("required", true);
  } else {
    document.getElementById("is_use_time").checked = false;
    response_time_start.removeAttribute("required");
    response_time_end.removeAttribute("required");
    input_time_response.classList.add("d-none");
    response_time_start.value = "";
    response_time_end.value = "";
  }

  if (data.is_use_ai) {
    document.getElementById("is_use_ai").checked = true;
  } else {
    document.getElementById("is_use_ai").checked = false;
  }

  document.getElementById("title-detail").innerHTML = data.title;
  document.getElementById("last_updated").innerHTML = `<span title="${
    DateTimeTH ? DateTimeTH : ""
  }">${relativeTime ? relativeTime : ""}</span>`;
  document.getElementById("caption").innerHTML = "";
  document.getElementById("response_title").value = data.title;
  document.getElementById("type").value = data.type;
  document.getElementById("data_response").value = data.data_response
    ? data.data_response
    : "";
  document.getElementById("altText").value = data.altText ? data.altText : "";
  document.getElementById("event_type").value = res[0].event_type;

  let change_event = new Event("change");
  input_type.dispatchEvent(change_event);

  let check_is_regex = document.getElementById("is_regex");
  res.forEach((item) => {
    let is_regex = Number(item.is_regex);
    check_is_regex.checked = is_regex ? true : false;
    let option = document.createElement("option");
    option.value = item.caption;
    option.text = item.caption;
    option.selected = true;
    option.dataset.is_regex = is_regex;
    document.getElementById("caption").appendChild(option);
  });

  $("#caption").select2({
    theme: "bootstrap-5",
    width: $(this).data("width")
      ? $(this).data("width")
      : $(this).hasClass("w-100")
      ? "100%"
      : "style",
    closeOnSelect: false,
    tags: true,
    createTag: (params) => {
      return {
        id: params.term,
        text: params.term,
        newOption: true,
      };
    },
    templateResult: (data, container) => {
      let is_regex;
      let check_is_regex = document.getElementById("is_regex").checked ? 1 : 0;

      if (data.element) {
        is_regex = data.element.dataset.is_regex
          ? data.element.dataset.is_regex
          : check_is_regex;
      }

      let $result = $("<div></div>");
      $result.html(`<div class="d-flex justify-content-between">
            <span>${data.text}</span>
            ${
              data.newOption
                ? `<span class="badge bg-label-success">NEW</span>`
                : ``
            }
        </div>`);

      return $result;
    },
  });

  /* $('#caption').on('select2:select', function (e) {
    // Do something
    console.log(e)
    let text = e.params.data.text;
    e.params.data.text = `${text}|value`;
}); */
}

//function filter data from DataTable
function filterDataFromDataTable(key, value) {
  let result = getAjaxFromDataTable().filter((item) => {
    return item[key] == value;
  });
  return result;
}

//function get ajax from DataTable
function getAjaxFromDataTable(table = "#table_bot_title") {
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
function initDataTable() {
  //destroy table
  $("#table_bot_title").DataTable().clear().destroy();
  showLoading();

  table_bot_title = new DataTable("#table_bot_title", {
    ajax: {
      url: `${$APP_URL}/line/api/get-bot-response`,
      type: "POST",
    },
    processing: true,
    serverSide: true,
    screenX: "100%",
    scrollX: true,
    scrollY: "75vh",
    sScrollX: "100%",
    autoWidth: true,
    sScrollXInner: "100%",
    scrollCollapse: true,
    deferRender: true,
    scroller: {
      loadingIndicator: true,
    },
    buttons: [
      {
        text: "Add New",
        action: function (e, dt, node, config) {
          //dt.ajax.reload();
          let result = ShowAddForm();
          console.log(result);
        },
        className: "btn btn-primary btn-sm waves-effect",
        attr: {
          id: "btn_add_response",
          title: "เพิ่มข้อมูลใหม่",
          /* 'data-bs-target': '#basicModal',
                'data-bs-toggle': 'modal' */
        },
      },
      {
        text: '<div title="Refresh"><span class="mdi mdi-refresh"></span></div>',
        action: function (e, dt, node, config) {
          dt.ajax.reload();
        },
        className: "btn btn-primary btn-sm waves-effect",
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Thai.json",
    },
    columns: [
      {
        data: "title",
        render: (data, type, row) => {
          let result = data ? data : "-";
          let id = row.id ? row.id : "";
          let link = ` href="javascript:void(0)" title="ดูข้อมูลเพิ่มเติม" onclick="ShowEditForm(this)" data-id="${id}" `;
          let html = `<div class="col-12">
                    <span class="text-center text-truncate text-primary " ${link}>
                        <a ${link}>${result}</a>
                    </span>
                </div>`;
          return html;
        },
      },
      {
        data: "altText",
        render: (data, type, row) => {
          let result = data ? data : "-";
          let html = `<div class="col-12">
                    <span class="text-center text-truncate">
                        ${result}
                    </span>
                </div>`;
          return html;
        },
      },
      {
        data: "event_type",
        render: (data, type, row) => {
          let result = data ? data.toLocaleUpperCase() : "-";
          let html = `<div class="col-12 d-flex justify-content-center">
                    <span class="badge bg-info">
                        ${result}
                    </span>
                </div>`;
          return html;
        },
      },
      {
        data: "type",
        render: (data, type, row) => {
          let result = data ? data.toLocaleUpperCase() : "-";
          let html = `<div class="col-12 d-flex justify-content-center">
                    <span class="badge ${
                      result == "TEXT" ? `bg-info` : `bg-success`
                    }">
                        ${result}
                    </span>
                </div>`;
          return html;
        },
      },
      {
        data: "response_count",
        render: (data, type, row) => {
          let result = data ? data.toLocaleString("th-TH") : "-";
          let html = `<div class="col-12 d-flex justify-content-center">
                    ${result}
                </div>`;
          return html;
        },
      },
      {
        data: "is_use_time",
        render: (data, type, row) => {
          let html = "";
          if (data) {
            html = `<div class="col-12 d-flex justify-content-center">
                        <span class="badge bg-label-success">ใช้งาน</span>
                    </div>`;
          } else {
            html = `<div class="col-12 d-flex justify-content-center">
                        <span class="badge bg-label-danger">ไม่ใช้งาน</span>
                    </div>`;
          }
          return html;
        },
      },
      {
        data: "active",
        render: (data, type, row) => {
          let result = data ? data : "-";
          let result_text = result == "1" ? "เปิด" : "ปิด";
          let id = row.id ? row.id : "";
          let is_checked = result == "1" ? "checked" : "";
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
                </div>`;
          return html;
        },
      },
      {
        data: "id",
        render: (data, type, row) => {
          let created = row.created ? convertToThaiDateTime(row.created) : "-";

          let id = data ? data : "-";
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
                </div>`;
          return html;
        },
      },
    ],
    order: [[5, "desc"]],
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
    initComplete: (settings, json) => {},
  })
    // on DataTable Draw Data
    .on("draw.dt", function (e, dt, type, indexes) {
      console.log("draw.dt", e, dt, type, indexes);
      document
        .querySelectorAll(".form-active-status")
        .forEach(checkBoxOnChange);
      hideLoading();
    })
    .on("xhr", function (e, settings, json) {
      console.log("Ajax event occurred. Returned data: ", json);
    });
}

function convertToThaiDateTime(date) {
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "numeric",
    minute: "numeric",
    second: "numeric",
  };
  const thaiDate = new Date(date).toLocaleString("th-TH", options);
  //const [day, month, yearAndTime] = thaiDate.split('/');
  //const [year, time] = yearAndTime.split(', ');
  return `${thaiDate}`;
}

function checkBoxOnChange(checkbox) {
  //remove event
  checkbox.removeEventListener("change", () => {});

  //add event
  checkbox.addEventListener("change", async (event) => {
    let id = checkbox.dataset.id;
    let value = checkbox.checked ? 1 : 0;
    let Labeltext = value ? "เปิด" : "ปิด";
    let formData = new FormData();
    formData.append("id", id);
    formData.append("active", value);
    let res = await activeBotResponse(formData);
    if (res.status != 200) {
      ShowToast({
        title: "Error",
        body: "Update Error",
        icon: '<i class="mdi mdi-close-circle-outline me-2 text-danger"></i>',
      });
      return;
    }
    ShowToast({
      title: "Success",
      body: `${Labeltext}สำเร็จ`,
      icon: '<i class="mdi mdi-check-circle-outline me-2 text-success"></i>',
    });
    event.target.title = Labeltext;
  });
}

async function editBotResponse(form) {
  if (!(form instanceof FormData)) {
    console.error("editBotResponse", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/edit-bot-response`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function CheckCaptionDuplicate(form) {
  if (!(form instanceof FormData)) {
    console.error("CheckCaptionDuplicate", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/check-caption-duplicate`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function addBotResponse(form) {
  if (!(form instanceof FormData)) {
    console.error("addBotResponse", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/add-bot-response`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function getBotCaption(form) {
  if (!(form instanceof FormData)) {
    console.error("getBotCaption", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/get-bot-caption`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function DeleteResponse(id) {
  if (!id) {
    return;
  }

  //instanceof swal2
  let swal_res = await Swal.fire({
    icon: "question",
    html: `คุณต้องการลบข้อมูลนี้ใช่หรือไม่ ?`,
    showCancelButton: true,
  });

  //check isConfirm
  if (!swal_res.isConfirmed) {
    return;
  }

  //call api delete
  let formData = new FormData();
  formData.set("id", id);
  let res = await DeleteBotResponse(formData);
  if (res.status != 200) {
    ShowToast({
      title: "Error",
      body: "Delete Error",
      icon: '<i class="mdi mdi-close-circle-outline me-2 text-danger"></i>',
    });
    return;
  }
  ShowToast({
    title: "Success",
    body: "Delete Success",
    icon: '<i class="mdi mdi-check-circle-outline me-2 text-success"></i>',
  });
  let Close_res = await CloseDetail("finished");
  initDataTable();
}

async function getBotResponse(form) {
  if (!(form instanceof FormData)) {
    console.error("getBotResponse", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/get-bot-response`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function DeleteBotResponse(form) {
  if (!(form instanceof FormData)) {
    console.error("DeleteBotResponse", "first parameter must be FormData");
    return false;
  }
  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/delete-bot-response`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

async function activeBotResponse(form) {
  if (!(form instanceof FormData)) {
    console.error("activeBotResponse", "first parameter must be FormData");
    return false;
  }

  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/active-bot-response`,
    requestOptions
  );
  const status = response.status;
  if (status !== 200) {
    return false;
  }

  const result = await response.json();
  return result;
}

///test-push-message
async function testPushMessage(id = "") {
  let form = new FormData();
  let data_response = document.getElementById("data_response").value
    ? document.getElementById("data_response").value
    : "";
  let type = document.getElementById("type").value
    ? document.getElementById("type").value
    : "";
  console.log(event);
  form.set("id", id);
  form.set("data_response", data_response);
  form.set("type", type);

  let btn = event.target;

  //loop until btn is button
  while (btn.tagName != "A" && btn.tagName != "BUTTON") {
    btn = btn.parentElement;
  }

  if (!id && (!data_response || !type)) {
    Swal.fire({
      icon: "warning",
      title: "Warning",
      text: "กรุณากรอกข้อมูลให้ครบถ้วน",
    });
    return;
  }

  //set bootstrap5 loading to btn
  btn.setAttribute("disabled", true);
  btn.innerHTML = `
    <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
    <span role="status">Loading...</span>
`;

  const myHeaders = new Headers();
  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: form,
    redirect: "manual",
  };
  let response = await fetch(
    `${$API_URL}/test-push-message`,
    requestOptions
  );
  const status = response.status;

  btn.removeAttribute("disabled");
  btn.innerHTML = `<span class="mdi mdi-check-bold mx-auto"></span></i> สำเร็จ`;
  setTimeout(() => {
    btn.innerHTML = `<i class="mdi mdi-send-circle-outline mx-auto"></i> ทดสอบส่ง`;
  }, 1000);

  if (status !== 200) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "เกิดข้อผิดพลาด ไม่สามารถทดสอบส่งข้อความได้ โปรดลองใหม่อีกครั้ง",
    });
    return false;
  }

  const result = await response.json();
  return result;
}

function ShowToast(
  params = {
    title: "",
    body: "",
    icon: "",
  }
) {
  let title = params.title ? params.title : "";
  let body = params.body ? params.body : "";
  let icon = params.icon
    ? params.icon
    : '<i class="mdi mdi-home me-2 text-danger"></i>';
  document.getElementById("toast-header").innerHTML = title;
  document.getElementById("toast-body").innerHTML = body;
  document.getElementById("toast-header-icon").innerHTML = icon;
  toastElList.show();
}
