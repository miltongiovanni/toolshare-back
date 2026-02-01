import DataTable from 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import languageEn from 'datatables.net-plugins/i18n/en-GB.mjs';
import languageFr from 'datatables.net-plugins/i18n/fr-FR.mjs';
import languageEs from 'datatables.net-plugins/i18n/es-CO.mjs';
//Import Dropzone
import Dropzone from "dropzone";
// Optionally, import the dropzone file to get default styling.
import "dropzone/dist/dropzone.css";
Dropzone.autoDiscover = false;
import {DateTime} from "luxon";
import {Modal} from 'bootstrap';

const productCategoryModal = new Modal(document.getElementById('productCategoryModal'));
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
        {
            data: 'name',
            width: '20%',
        },
        {
            data: 'description',
            width: '20%',
        },
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
                let actionTitle = row.enabled ? t('disable') : t('enable');
                let actionClass = row.enabled ? 'category-enabled' : 'category-disabled';
                return '<div class="dropdown">\n' +
                    '    <button type="button" class="btn btn-primary dropdown-toggle w-100" role="button" id="dropdownMenuLink"\n' +
                    '       data-bs-toggle="dropdown" aria-expanded="false">\n' + t('actions') +
                    '    </button>\n' +
                    '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-category-id="' + row.id + '"  type="button" class="dropdown-item ' + actionClass + '" >' + actionTitle + '</button>\n' +
                    '        </li>\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-category-id="' + row.id + '"  type="button" class="dropdown-item category-edit" >' + t('edit') + '</button>\n' +
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


$(document).on('click', '#add_category_button', function () {
    $('#productCategoryModalLabel').html(t('product.category.add'));
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
        let url = '/product/category/update';
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
                categoriesDataTable.ajax.reload();
                productCategoryModal.hide();
            },
            error: function (xhr, status, error) {
                // Handle errors
                $('#response-message').html('Error: ' + error);
                console.error(xhr.responseText);
            }
        });
    }
});

$(document).on('click', '#add_category_button', function () {
    $('#productCategoryModalLabel').html(t('product.category.add'));
    $('#category_id').val(0)
});
const productCategoryModalEl = document.getElementById('productCategoryModal');
productCategoryModalEl.addEventListener('hidden.bs.modal', event => {
    const dz = document.getElementById("image_upload")?.dropzone;

    if (dz) {
        dz.removeAllFiles(true);
    }
    $('#category_form').trigger("reset").removeClass("was-validated");
    $('#img-product').addClass('d-none').attr('src', '');
    $('#dropzoneTitle').show();

})
productCategoryModalEl.addEventListener('shown.bs.modal', event => {
    const dzElement = document.getElementById("image_upload");
    if (!dzElement.dropzone) {
        new Dropzone("div#image_upload",
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
    }

})
$(document).on('click', '.category-enabled', function () {
    let category_id = $(this).data('categoryId');
    $.ajax({
        url: '/product/category/disable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            category_id: category_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            categoriesDataTable.ajax.reload();
            productCategoryModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});


$(document).on('click', '.category-disabled', function () {
    let category_id = $(this).data('categoryId');
    $.ajax({
        url: '/product/category/enable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            category_id: category_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            categoriesDataTable.ajax.reload();
            productCategoryModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});
$(document).on('click', '.category-edit', function () {
    let category_id = $(this).data('categoryId');
    $.ajax({
        url: '/product/category/get', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            category_id: category_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#enabled').prop('checked', response.enabled);
            $('#image').val(response.image);
            let imgUrl = app_url + 'uploads/images/' + response.image;
            $('#img-product').attr('src', imgUrl).removeClass('d-none');
            $('#dropzoneTitle').hide();
            $('#name_en').val(response.name_en);
            $('#name_fr').val(response.name_fr);
            $('#name_es').val(response.name_es);
            $('#description_en').val(response.description_en);
            $('#description_fr').val(response.description_fr);
            $('#description_es').val(response.description_es);

            $('#productCategoryModalLabel').html(t('product.category.edit'));
            $('#category_id').val(category_id);
            productCategoryModal.show();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});