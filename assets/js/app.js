/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
require('bootstrap');

let slots = document.querySelectorAll('.time-slot .slot');
var mouseIsDown = false;
var currentState = false;

Array.from(slots).forEach(slot => {
    slot.addEventListener('mousedown', function () {
        mouseIsDown = true;
        currentState = !this.classList.contains('bg-success');
        this.classList.toggle('bg-success');
    });
    slot.addEventListener('mouseup', function () {
        mouseIsDown = false;
    });

    slot.addEventListener('mouseenter', function () {
        if (mouseIsDown) {
            if (currentState) {
                this.classList.add('bg-success');
            } else {
                this.classList.remove('bg-success');
            }
        }
    });
});
