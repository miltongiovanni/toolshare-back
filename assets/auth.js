/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './css/vendors/font-awesome.css';
import './css/vendors/themify-icons.css';
import './css/vendors/flag-icon.css';
import './css/vendors/icofont.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import './css/style.css';
import './css/auth.css';
//import './styles/app.css';


import $ from 'jquery';
// things on "window" become global variables
window.$ = $;

import 'bootstrap';
import featherIcons from "feather-icons";

featherIcons.replace();


$(document).ready(function () {
    $(".toggle-password").click(function () {
        // Toggle the eye icon class
        $(this).toggleClass("fa-eye fa-eye-slash");

        // Target input and switch type
        var input = $($(this).attr("toggle"));
        input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });
});

var elements = document.getElementsByClassName("validate-password");
var myInput = elements[0];
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var special = document.getElementById("special");
var length = document.getElementById("length");

if (myInput !== undefined) {
    // When the user clicks on the password field, show the message box
    myInput.onfocus = function () {
        document.getElementById("message").style.display = "block";
    }

// When the user clicks outside of the password field, hide the message box
    myInput.onblur = function () {
        document.getElementById("message").style.display = "none";
    }

    var newPasswordValid;
    var confirmPasswordValid;
// When the user starts to type something inside the password field
    myInput.onkeyup = function () {
        let valid = 0;
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
            valid++;
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
            valid++;
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
            valid++;
        }
        // Validate numbers
        var specials = /[!@#$%^&*(),.?":{}|<>_\-\\[\]\/+=~`]/;
        if (myInput.value.match(specials)) {
            special.classList.remove("invalid");
            special.classList.add("valid");
        } else {
            special.classList.remove("valid");
            special.classList.add("invalid");
            valid++;
        }

        // Validate length
        if (myInput.value.length >= 8) {
            length.classList.remove("invalid");
            length.classList.add("valid");
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
            valid++;
        }
        newPasswordValid = valid === 0;
    }
}

if (document.getElementById("confirm_password") != null) {
    document.getElementById("confirm_password").addEventListener("keyup", function () {
        let confirmValid = 0;
        let password_no_match = document.getElementById("password_no_match");
        let submit_reset_password_form = document.getElementById("submit_reset_password_form");
        if (document.getElementById("new_password").value !== this.value) {
            password_no_match.classList.remove("d-none");
            if (submit_reset_password_form != null) {
                submit_reset_password_form.disabled = true;
            }
            confirmValid++;
        } else {
            password_no_match.classList.add("d-none");
            if (submit_reset_password_form != null) {
                submit_reset_password_form.disabled = false;
            }
        }
        confirmPasswordValid = confirmValid === 0;
    });
}

if (document.getElementById("old_password") != null) {
    document.getElementById("old_password").addEventListener("keyup", function () {
        let submit_change_password_form = document.getElementById("submit_change_password_form");
        submit_change_password_form.disabled = !(this.value.length > 3 && newPasswordValid && confirmPasswordValid);
    });
}

if (document.getElementById("change_password_form") != null) {
    document.getElementById("change_password_form").addEventListener("submit", function () {
        let valid = 0;
        let password_no_match = document.getElementById("password_no_match");
        if (document.getElementById("new_password").value !== document.getElementById("confirm_password").value) {
            password_no_match.classList.remove("d-none");
            valid++;
        }
        if (valid > 0) {
            return false;
        }
    });
}

if (document.getElementById("reset_password_form") != null) {
    document.getElementById("reset_password_form").addEventListener("submit", function () {
        let valid = 0;
        let password_no_match = document.getElementById("password_no_match");
        if (document.getElementById("new_password").value !== document.getElementById("confirm_password").value) {
            password_no_match.classList.remove("d-none");
            valid++;
        }
        if (valid > 0) {
            return false;
        }
    });
}
