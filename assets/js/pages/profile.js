import DataTable from 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import languageEn from 'datatables.net-plugins/i18n/en-GB.mjs';
import languageFr from 'datatables.net-plugins/i18n/fr-FR.mjs';
import languageEs from 'datatables.net-plugins/i18n/es-CO.mjs';
//Import Dropzone
import {DateTime} from "luxon";
import {Modal} from 'bootstrap';

const profileModal = new Modal(document.getElementById('profileModal'));
let language = locale === 'en' ? languageEn : (locale === 'fr' ? languageFr : languageEs);

let profileDataTable = new DataTable("#profileDataTable", {
    ajax: {
        url: '/profile/list',
        type: "POST",
        dataType: "json"
    }, columns: [
        {
            data: 'id',
            width: '5%',
        },
        {
            data: 'code',
            width: '20%',

        },
        {
            data: 'slug',
            width: '20%',
        },
        {
            data: 'description',
            width: '20%',
        },
        {
            data: 'created_at',
            width: '10%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toISODate();
            }
        },
        {
            data: 'updated_at',
            width: '10%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toISODate();
            }
        },
        {
            data: 'enabled',
            width: '5%',
            render: function (data, type, row) {
                return data === true ? '<i class="bi bi-check-circle-fill text-success fs-4"></i>' : '<i class="bi bi-x-circle-fill text-danger fs-4"></i>';
            }
        },
        {
            data: 'null',
            width: '10%',
            render: function (data, type, row) {
                let actionTitle = row.enabled ? t('disable') : t('enable');
                let actionClass = row.enabled ? 'profile-enabled' : 'profile-disabled';
                return '<div class="dropdown">\n' +
                    '    <button type="button" class="btn btn-primary dropdown-toggle w-100" role="button" id="dropdownMenuLink"\n' +
                    '       data-bs-toggle="dropdown" aria-expanded="false">\n' + t('actions') +
                    '    </button>\n' +
                    '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-profile-id="' + row.id + '"  type="button" class="dropdown-item profile-edit" >' + t('edit') + '</button>\n' +
                    '        </li>\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-profile-id="' + row.id + '"  type="button" class="dropdown-item ' + actionClass + '" >' + actionTitle + '</button>\n' +
                    '        </li>\n' +
                    '    </ul>\n' +
                    '</div>'
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

$(document).on('click', '#submit_profile', function () {
    let form = document.getElementById('profile_form');
    form.classList.add('was-validated');
    if (form.checkValidity()) {
        let url = '/profile/update';
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
                profileDataTable.ajax.reload();
                profileModal.hide();
            },
            error: function (xhr, status, error) {
                // Handle errors
                $('#response-message').html('Error: ' + error);
                console.error(xhr.responseText);
            }
        });
    }
});

const profileModalEl = document.getElementById('profileModal');

$(document).on('click', '#add_profile_button', function () {
    $('#profileModalLabel').html(t('profile.add'));
    $('#profile_id').val(0)
});

profileModalEl.addEventListener('hidden.bs.modal', event => {
    $('#profile_form').trigger("reset").removeClass("was-validated");
    $('#english-tab').trigger("click");

})

$(document).on('click', '.profile-enabled', function () {
    let profile_id = $(this).data('profileId');
    $.ajax({
        url: '/profile/disable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            profile_id: profile_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            profileDataTable.ajax.reload();
            profileModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});


$(document).on('click', '.profile-disabled', function () {
    let profile_id = $(this).data('profileId');
    $.ajax({
        url: '/profile/enable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            profile_id: profile_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            profileDataTable.ajax.reload();
            profileModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});

$(document).on('click', '.profile-edit', function () {
    let profile_id = $(this).data('profileId');
    $.ajax({
        url: '/profile/get', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            profile_id: profile_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#enabled').prop('checked', response.enabled);
            $('#code').val(response.code);

            $('#slug_en').val(response.slug_en);
            $('#slug_fr').val(response.slug_fr);
            $('#slug_es').val(response.slug_es);
            $('#description_en').val(response.description_en);
            $('#description_fr').val(response.description_fr);
            $('#description_es').val(response.description_es);

            $('#profileModalLabel').html(t('profile.edit'));
            $('#profile_id').val(response.profile_id);
            profileModal.show();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});


$(document).on('keyup', '#description_en', function () {
    $('#slug_en').val($(this).val().trim().toLowerCase().replace(' ', '-'))
    $('#code').val('ROLE_' + $(this).val().trim().toUpperCase().replace(' ', '_'));
});

$(document).on('keyup', '#description_fr', function () {
    $('#slug_fr').val($(this).val().trim().toLowerCase().replace(' ', '-'));
});

$(document).on('keyup', '#description_es', function () {
    $('#slug_es').val($(this).val().trim().toLowerCase().replace(' ', '-'));
});