/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery'; // installed using 'yarn add jquery --save-dev --ignore-engines'
global.$ = global.jQuery = $; // this makes jquery available for the entire application.

import 'bootstrap'; // installed using 'yarn add bootstrap --save-dev --ignore-engines'
import 'bootstrap-datepicker'; // installed using 'yarn add bootstrap-datepicker --save-dev --ignore-engines'

import axios from 'axios';// installed using 'yarn add axios --save-dev --ignore-engines'
global.axios = axios;