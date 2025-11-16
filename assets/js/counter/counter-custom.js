import '../counter/jquery.waypoints.min.js';
import '../counter/jquery.counterup.min.js';


(function($) {
    "use strict";
    $('.counter').counterUp({
        delay: 10,
        time: 1000
    });
})($);
