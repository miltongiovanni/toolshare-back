import DataTable from 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import languageEn from 'datatables.net-plugins/i18n/en-GB.mjs';
import languageFr from 'datatables.net-plugins/i18n/fr-FR.mjs';
import languageEs from 'datatables.net-plugins/i18n/es-CO.mjs';
//Import Dropzone
import {DateTime} from "luxon";
import {Modal} from 'bootstrap';

const userAdminModal = new Modal(document.getElementById('userAdminModal'));
let language = locale === 'en' ? languageEn : (locale === 'fr' ? languageFr : languageEs);

let userDataTable = new DataTable("#userDataTable", {
    ajax: {
        url: '/user/admin/list',
        type: "POST",
        dataType: "json"
    }, columns: [
        {
            data: 'id',
            width: '5%',
        },
        {
            data: 'first_name',
            width: '15%',

        },
        {
            data: 'last_name',
            width: '15%',

        },
        {
            data: 'email',
            width: '25%',
        },
        {
            data: 'description',
            width: '15%',
        },
        {
            data: 'created_at',
            width: '10%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toLocaleString(DateTime.DATETIME_MED);
            }
        },
        {
            data: 'last_login',
            width: '10%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toLocaleString(DateTime.DATETIME_MED);
            }
        },
        {
            data: 'is_active',
            width: '5%',
            render: function (data, type, row) {
                return data === true ? '<i class="bi bi-check-circle-fill text-success fs-4"></i>' : '<i class="bi bi-x-circle-fill text-danger fs-4"></i>';
            }
        },
        {
            data: 'null',
            width: '10%',
            render: function (data, type, row) {
                let actionTitle = row.is_active ? t('disable') : t('enable');
                let actionClass = row.is_active ? 'adminUser-enabled' : 'adminUser-disabled';
                let response = '';
                if (!row.isCurrentAdminUser){
                    response = '<div class="dropdown">\n' +
                        '    <button type="button" class="btn btn-primary dropdown-toggle w-100" role="button" id="dropdownMenuLink"\n' +
                        '       data-bs-toggle="dropdown" aria-expanded="false">\n' + t('actions') +
                        '    </button>\n' +
                        '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">\n' +
                        '        <li class="w-100">\n' +
                        '            <button data-adminUser-id="' + row.id + '"  type="button" class="dropdown-item ' + actionClass + '" >' + actionTitle + '</button>\n' +
                        '        </li>\n' +
                        '        <li class="w-100">\n' +
                        '            <button data-adminUser-id="' + row.id + '"  type="button" class="dropdown-item adminUser-edit" >' + t('edit') + '</button>\n' +
                        '        </li>\n' +
                        '    </ul>\n' +
                        '</div>';
                }
                return response;
            }
        }
    ],
    "columnDefs":
        [
            {
                "targets": [0, 6, 7],
                "className": 'dt-body-center'
            },
            {
                "targets": [6, 7],
                "orderable": false
            }
        ],
    language: language,
    order: [[1, 'asc'], [2, 'asc']]
});




// (function () {
//     'use strict'
//     // Fetch all the forms we want to apply custom Bootstrap validation styles to
//     var forms = document.querySelectorAll('.needs-validation')
//     // Loop over them and prevent submission
//     Array.prototype.slice.call(forms)
//         .forEach(function (form) {
//             form.addEventListener('submit', function (event) {
//                 console.log(event);
//                 if (!form.checkValidity()) {
//                     event.preventDefault()
//                     event.stopPropagation()
//                 }
//                 form.classList.add('was-validated')
//             }, false)
//         })
// })()

$(document).on('click', '#submit_user', function () {
    let form = document.getElementById('user_form');
    form.classList.add('was-validated');
    if (form.checkValidity()) {
        let url = '/user/admin/update';
        // Create a FormData object from the form
        var formData = new FormData(form);
        $.ajax({
            url: url, // Use the form's action attribute
            type: 'post', // Use the form's method attribute
            data: formData,
            processData: false, // Prevents jQuery from transforming the data into a query string
            contentType: false, // Prevents jQuery from setting the Content-Type header
            dataType: "json",
            success: function (response) {
                // Handle a successful response from the server
                $('#response-message').html('Success: ' + JSON.stringify(response));
                userDataTable.ajax.reload();
                userAdminModal.hide();
            },
            error: function (xhr, status, error) {
                // Handle errors
                $('#response-message').html('Error: ' + error);
                console.error(xhr.responseText);
            }
        });
    }
});
$(document).on('click', '#add_user_button', function () {
    $('#userAdminModalLabel').html(t('user.admin.add'));
    $('#user_id').val(0)
});
const userAdminModalEl = document.getElementById('userAdminModal');
userAdminModalEl.addEventListener('hidden.bs.modal', event => {
    $('#user_form').trigger("reset").removeClass("was-validated");
    $('#english-tab').trigger("click");

})

$(document).on('click', '.adminUser-enabled', function () {
    let adminUser_id = $(this).data('adminuserId');
    $.ajax({
        url: '/user/admin/disable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            adminUser_id: adminUser_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            userDataTable.ajax.reload();
            userAdminModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});


$(document).on('click', '.adminUser-disabled', function () {
    let adminUser_id = $(this).data('adminuserId');
    $.ajax({
        url: '/user/admin/enable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            adminUser_id: adminUser_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            userDataTable.ajax.reload();
            userAdminModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});
$(document).on('click', '.adminUser-edit', function () {
    let adminUser_id = $(this).data('adminuserId');
    $.ajax({
        url: '/user/admin/get', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            adminUser_id: adminUser_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#is_active').prop('checked', response.is_active);
            $('#user_id').val(response.id)
            $('#first_name').val(response.first_name);
            $('#last_name').val(response.last_name);
            $('#email').val(response.email);
            $('#profile_id').val(response.profile);
            $('#userAdminModalLabel').html(t('user.admin.edit'));
            userAdminModal.show();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});