import DataTable from 'datatables.net-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import languageEn from 'datatables.net-plugins/i18n/en-GB.mjs';
import languageFr from 'datatables.net-plugins/i18n/fr-FR.mjs';
import languageEs from 'datatables.net-plugins/i18n/es-CO.mjs';
//Import Dropzone
import {DateTime} from "luxon";
import {Modal} from 'bootstrap';

const productSubcategoryModal = new Modal(document.getElementById('productSubcategoryModal'));
let language = locale === 'en' ? languageEn : (locale === 'fr' ? languageFr : languageEs);

let subcategoriesDataTable = new DataTable("#subcategoriesDataTable", {
    ajax: {
        url: '/product/subcategory/list',
        type: "POST",
        dataType: "json"
    }, columns: [
        {
            data: 'id',
            width: '5%',
        },
        {
            data: 'category',
            width: '20%',

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
                let actionClass = row.enabled ? 'subcategory-enabled' : 'subcategory-disabled';
                return '<div class="dropdown">\n' +
                    '    <button type="button" class="btn btn-primary dropdown-toggle w-100" role="button" id="dropdownMenuLink"\n' +
                    '       data-bs-toggle="dropdown" aria-expanded="false">\n' + t('actions') +
                    '    </button>\n' +
                    '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-subcategory-id="' + row.id + '"  type="button" class="dropdown-item ' + actionClass + '" >' + actionTitle + '</button>\n' +
                    '        </li>\n' +
                    '        <li class="w-100">\n' +
                    '            <button data-subcategory-id="' + row.id + '"  type="button" class="dropdown-item subcategory-edit" >' + t('edit') + '</button>\n' +
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

$(document).on('click', '#submit_category', function () {
    let form = document.getElementById('subcategory_form');
    form.classList.add('was-validated');
    if (form.checkValidity()) {
        let url = '/product/subcategory/update';
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
                subcategoriesDataTable.ajax.reload();
                productSubcategoryModal.hide();
            },
            error: function (xhr, status, error) {
                // Handle errors
                $('#response-message').html('Error: ' + error);
                console.error(xhr.responseText);
            }
        });
    }
});
$(document).on('click', '#add_subcategory_button', function () {
    $('#productSubcategoryModalLabel').html(t('product.subcategory.add'));
    $('#subcategory_id').val(0)
});
const productSubcategoryModalEl = document.getElementById('productSubcategoryModal');
productSubcategoryModalEl.addEventListener('hidden.bs.modal', event => {
    $('#subcategory_form').trigger("reset").removeClass("was-validated");
    $('#english-tab').trigger("click");

})

$(document).on('click', '.subcategory-enabled', function () {
    let subcategory_id = $(this).data('subcategoryId');
    $.ajax({
        url: '/product/subcategory/disable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            subcategory_id: subcategory_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            subcategoriesDataTable.ajax.reload();
            productSubcategoryModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});


$(document).on('click', '.subcategory-disabled', function () {
    let subcategory_id = $(this).data('subcategoryId');
    $.ajax({
        url: '/product/subcategory/enable', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            subcategory_id: subcategory_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#response-message').html('Success: ' + JSON.stringify(response));
            subcategoriesDataTable.ajax.reload();
            productSubcategoryModal.hide();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});
$(document).on('click', '.subcategory-edit', function () {
    let subcategory_id = $(this).data('subcategoryId');
    $.ajax({
        url: '/product/subcategory/get', // Use the form's action attribute
        type: 'post', // Use the form's method attribute
        data: {
            subcategory_id: subcategory_id,
        },
        dataType: "json",
        success: function (response) {
            // Handle a successful response from the server
            $('#enabled').prop('checked', response.enabled);

            $('#name_en').val(response.name_en);
            $('#name_fr').val(response.name_fr);
            $('#name_es').val(response.name_es);
            $('#description_en').val(response.description_en);
            $('#description_fr').val(response.description_fr);
            $('#description_es').val(response.description_es);

            $('#productSubcategoryModalLabel').html(t('product.category.edit'));
            $('#subcategory_id').val(subcategory_id);
            $('#product_category_id').val(response.category_id);
            productSubcategoryModal.show();
        },
        error: function (xhr, status, error) {
            // Handle errors
            $('#response-message').html('Error: ' + error);
            console.error(xhr.responseText);
        }
    });
});