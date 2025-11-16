
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
//import './styles/app.css';


import $ from 'jquery';
// things on "window" become global variables
window.$ = $;

import 'bootstrap';
import featherIcons from "feather-icons";
featherIcons.replace();