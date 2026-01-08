
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './css/vendors/font-awesome.css';
import './css/vendors/flag-icon.css';
import './css/vendors/icofont.css';
import './css/vendors/prism.css';
import './css/vendors/chartist.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import './css/style.css';
//import './styles/app.css';

import $ from 'jquery';
// things on "window" become global variables
window.$ = $;
window.jQuery = $;

import 'bootstrap';
import featherIcons from "feather-icons";
featherIcons.replace();
import "bootstrap-icons/font/bootstrap-icons.min.css"

(function ($) {
    "use strict";
    $(".mobile-toggle").click(function () {
        $(".nav-menus").toggleClass("open");
    });
    $(".mobile-search").click(function () {
        $(".form-control-plaintext").toggleClass("open");
    });
    $(".form-control-plaintext").keyup(function (e) {
        if (e.target.value) {
            $("body").addClass("offcanvas");
        } else {
            $("body").removeClass("offcanvas");
        }
    });
})($);

function toggleFullScreen() {
    if ((document.fullScreenElement && document.fullScreenElement !== null) ||
        (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
            document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
            document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
}
(function ($, window, document, undefined) {
    "use strict";
    var $ripple = $(".js-ripple");
    $ripple.on("click.ui.ripple", function (e) {
        var $this = $(this);
        var $offset = $this.parent().offset();
        var $circle = $this.find(".c-ripple__circle");
        var x = e.pageX - $offset.left;
        var y = e.pageY - $offset.top;
        $circle.css({
            top: y + "px",
            left: x + "px"
        });
        $this.addClass("is-active");
    });
    $ripple.on(
        "animationend webkitAnimationEnd oanimationend MSAnimationEnd",
        function (e) {
            $(this).removeClass("is-active");
        });
})($, window, document);

$(".chat-menu-icons .toogle-bar").click(function () {
    $(".chat-menu").toggleClass("show");
});


/*=====================
 05. Image to background js
 ==========================*/
$(".bg-img").parent().addClass('bg-size');

$('.bg-img').each(function () {

    var el = $(this),
        src = el.attr('src'),
        parent = el.parent();

    parent.css({
        'background-image': 'url(' + src + ')',
        'background-size': 'cover',
        'background-position': 'center',
        'display': 'block'
    });

    el.hide();
});