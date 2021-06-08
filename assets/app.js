import './styles/app.scss';
import './styles/missing.css'
import './bootstrap';

const $ = require('jquery');

require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});
