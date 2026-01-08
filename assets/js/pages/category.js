import DataTable from 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import languageEn from 'datatables.net-plugins/i18n/en-GB.mjs';
import languageFr from 'datatables.net-plugins/i18n/fr-FR.mjs';
import languageEs from 'datatables.net-plugins/i18n/es-CO.mjs';
//Import Dropzone
import Dropzone from "dropzone";
// Optionally, import the dropzone file to get default styling.
import "dropzone/dist/dropzone.css";
import {DateTime} from "luxon";


let language = locale === 'en' ? languageEn : (locale === 'fr' ? languageFr : languageEs);

let categoriesDataTable = new DataTable("#categoriesDataTable", {
    ajax: {
        url: '/product/category/list',
        type: "POST",
        dataType: "json"
    }, columns: [
        {
            data: 'id',
            width: '5%',
        },
        {
            data: 'image',
            width: '5%',
            render: function (data, type, row) {
                let imgUrl = app_url + 'uploads/images/' + data;
                return '<img class="img-fluid" src="' + imgUrl + '" alt="' + data + '">';
            }
        },
        {data: 'name',
            width: '20%',},
        {data: 'description',
            width: '20%',},
        {
            data: 'created_at',
            width: '15%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toLocaleString(DateTime.DATETIME_SHORT);
            }
        },
        {
            data: 'updated_at',
            width: '15%',
            render: function (data, type, row) {
                return DateTime.fromISO(data).setLocale(locale).toLocaleString(DateTime.DATETIME_SHORT);
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
                return '<div class="dropdown">\n' +
                    '    <a class="btn btn-primary dropdown-toggle w-100" href="#" role="button" id="dropdownMenuLink"\n' +
                    '       data-bs-toggle="dropdown" aria-expanded="false">\n' +
                    '        Acciones\n' +
                    '    </a>\n' +
                    '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">\n' +
                    '            <li>\n' +
                    (row.estado ? '<a class="dropdown-item" href="/cliente/' + row.slug + '/desactivar/">Desactivar</a>' : '<a class="dropdown-item" href="/cliente/' + row.slug + '/activar/">Activar</a>') +
                    '            </li>\n' +
                    '        <li>\n' +
                    '            <a class="dropdown-item" href="/cliente/' + row.slug + '/editar/">Editar</a>\n' +
                    '        </li>\n' +
                    '    </ul>\n' +
                    '</div>'
            }
        }
    ],
    "columnDefs":
        [
            {
                "targets": [0, 1, 6, 7],
                "className": 'dt-body-center'
            },
            {
                "targets": [6, 7],
                "orderable": false
            }
        ],
    language: language,
});


// $("div#image_upload").dropzone({
//     url: "/uploadImage",
//     maxFiles: 1,
//     dictMaxFilesExceeded: 'Only 1 Image can be uploaded',
//     acceptedFiles: 'image/*',
//     createImageThumbnails: true,
//     thumbnailMethod: 'contain',
//     maxFilesize: 3,  // in Mb
//     init: function () {
//         console.log('init');
//         this.on("success", function (file, response) {
//             console.log(response);
//             let imgUrl = $('#image_upload').data('base-url') + 'uploads/images/' + response.location;
//             //$('div.dz-success').remove();
//             $('#img-product').attr('src', imgUrl);
//             this.removeFile(file);
//             $('#dropzoneTitle').hide();
//             $('#img-preview').show();
//             $('#image').val(response.location);
//         });
//     }
// });

let myDropzone = new Dropzone("div#image_upload",
    {
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        url: "/uploadImage",
        maxFiles: 1,
        clickable: true,
        dictMaxFilesExceeded: 'Only 1 Image can be uploaded',
        acceptedFiles: 'image/*',
        createImageThumbnails: false,
        thumbnailMethod: 'contain',
        accept: function (file, done) {
            if (file.name === "justinbieber.jpg") {
                done("Naha, you don't.");
            } else {
                done();
            }
        },

        // When the complete upload is finished and successful
        // Receives `file`
        success(file) {
            $('#dropzoneTitle').hide();
            let response = JSON.parse(file.xhr.response);
            let imgUrl = $('#image_upload').data('base-url') + 'uploads/images/' + response.location;
            $('#img-product').attr('src', imgUrl).removeClass('d-none');
            $('#img-preview').show();
            $('#image').val(response.location);
        },
    });
$(document).on('click', '#add_category_button', function () {
    $('#category_id').val(0)
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

$(document).on('click', '#submit_category', function () {
    let form = document.getElementById('category_form');
    form.classList.add('was-validated');
    if (form.checkValidity()) {
        let category_id = $('#category_id').val();
        let url = '/product/category/' + category_id + '/update';
        // Create a FormData object from the form
        var formData = new FormData(form);
        console.log('form is valid');
        $.ajax({
            url: url, // Use the form's action attribute
            type: 'post', // Use the form's method attribute
            data: formData,
            processData: false, // Prevents jQuery from transforming the data into a query string
            contentType: false, // Prevents jQuery from setting the Content-Type header
            success: function (response) {
                // Handle a successful response from the server
                $('#response-message').html('Success: ' + JSON.stringify(response));
                console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle errors
                $('#response-message').html('Error: ' + error);
                console.error(xhr.responseText);
            }
        });
    }
});