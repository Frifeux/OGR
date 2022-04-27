/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

require('bootstrap/scss/bootstrap.scss'); // Bootstrap scss
require('bootstrap/dist/js/bootstrap'); // Bootstrap js
require('bootstrap-icons/font/bootstrap-icons.css'); // Icon bootstrap

require('@fortawesome/fontawesome-free/css/all.css'); // Font awesome css
require('@fortawesome/fontawesome-free/js/all.js'); // Font awesome js

require('flag-icons/css/flag-icons.min.css'); // Flag icon

import $ from 'jquery';
global.$ = global.jQuery = $;

// require('fullcalendar'); // Fullcalendar
// require('fullcalendar/main.css'); // Fullcalendar
// require('@fullcalendar/daygrid'); // Fullcalendar
// require('@fullcalendar/timegrid'); // Fullcalendar

// import { Calendar } from '@fullcalendar/core';
// import dayGridPlugin from '@fullcalendar/daygrid';
// import listPlugin from '@fullcalendar/list';
// import timeGridPlugin from '@fullcalendar/timegrid';
