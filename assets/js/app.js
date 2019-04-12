/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
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
    document.addEventListener('mouseup', function () {
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

import Chartist from 'chartist';
require('chartist-plugin-threshold');

var chart = new Chartist.Line('.ct-chart', {
    labels: ['8h',null,null,null,'9h',null,null,null,'10h',null,null,null,'11h',null,null,null,'12h',null,null,null,'13h',null,null,null,'14h',null,null,null,'15h',null,null,null,'16h',null,null,null,'17h',null,null,null,'18h',null,null,null,'19h',null,null,null,'20h',null,null,null,'21h',null,null,null,'22h',null,null,null,'23h',null,null,null,'0h',null,null,null,'1h',null,null,null,'2h',null,null,null,'3h',null,null,null,'4h',null,null,null],
    // Naming the series with the series object array notation
    series: [
        {
            name: 'series-1',
            data: [3,3,3,3,3,3,3,3,9,9,12,12,26,26,22,22,34,34,35,35,31,31,29,29,71,71,71,71,67,67,67,67,65,65,64,64,70,70,72,72,80,80,88,88,85,85,89,89,85,85,77,77,77,77,81,81,73,73,70,70,70,70,77,72,66,66,67,67,67,67,70,70,66,66,60,60,21,21,15,15,2,2,0,0]
        },
        {
            name: 'series-2',
            data: [3,3,3,3,3,3,3,3,9,9,12,12,28,28,24,24,36,36,36,36,32,32,30,30,73,73,73,73,69,69,69,69,65,65,65,65,70,70,72,72,79,79,85,85,83,83,88,88,85,85,77,77,77,77,81,81,73,73,71,71,70,70,76,71,66,66,66,66,67,67,70,70,66,66,61,61,22,22,15,15,3,3,1,1]
        }


    ]
}, {
    fullWidth: true,
    // Within the series options you can use the series names
    // to specify configuration that will only be used for the
    // specific series.
    series: {
        'series-1': {
            lineSmooth: Chartist.Interpolation.step(),
            showPoint: false,

        },

        'series-2': {
            lineSmooth: Chartist.Interpolation.step(),
            showPoint: false,
        }

    },
    //low: 0,
    axisY: {
        onlyInteger: true,
    },
    plugins: [
        Chartist.plugins.ctThreshold({
            threshold: 'series-1',// [12, 10, 12, 17, 10, 13, 12, 13, 15, 14, 12, 10, 10, 10, 11, 13, 12, 14, 14, 10, 12, 15, 10, 12, 15, 10, 12, 15, 12, 15,],
            lineSmooth: Chartist.Interpolation.step()
        }),
        Chartist.plugins.ctThreshold({
            threshold: 'series-2',// [12, 10, 12, 17, 10, 13, 12, 13, 15, 14, 12, 10, 10, 10, 11, 13, 12, 14, 14, 10, 12, 15, 10, 12, 15, 10, 12, 15, 12, 15,],
            lineSmooth: Chartist.Interpolation.step()
        })
    ]
});


import { Calendar } from '@fullcalendar/core';
import timelinePlugin from '@fullcalendar/timeline'
import common from '@fullcalendar/resource-common/main'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import bootstrap from "@fullcalendar/bootstrap";

import '@fullcalendar/core/main.css';
import '@fullcalendar/timeline/main.css';
import '@fullcalendar/resource-timeline/main.css';
import '@fullcalendar/bootstrap/main.css';

//import { Scheduler } from '@fullcalendar/scheduler'

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        plugins: [ resourceTimelinePlugin, bootstrap ],
        themeSystem: 'bootstrap',
        editable: true,
        bootstrapFontAwesome : {
            close: 'times',
            prev: 'chevron-left',
            next: 'chevron-right',
            prevYear: 'angle-double-left',
            nextYear: 'angle-double-right'
        },
        resourceAreaWidth: "20%",
        customButtons: {
            eventStart: {
                text: "Début de l'évenement",
                click: function() {
                    $('#timeline').fullCalendar('gotoDate', '{{ event.startDate|date("Y-m-d H:i:s") }}');
                }
            }
        },
        header: {
            left:   'title',
            center: '',
            right:  'eventStart today prev,next'
        },
        locale: 'fr',
        resources: 'https://fullcalendar.io/demo-resources.json?with-nesting&with-colors',
        events: 'https://fullcalendar.io/demo-events.json?single-day&for-resource-timeline',
        nowIndicator: true,
        defaultView: 'event',
        views: {
            event: {
                type: 'resourceTimeline',
                duration: { hours: 25 },
                slotDuration: '00:15',
                slotWidth: 20,
            }
        },
    });

    calendar.render();
});

/**
 * Handle search on event task page
 * TODO: seprate this code
 */
let filter = function () {
    let value = this.value.toLowerCase();
    let listItems = Array.from(document.querySelectorAll(".searchable-list .list-group-item"));
    listItems.forEach(el => {
       if (el.text.toLowerCase().indexOf(value) > -1) {
           el.classList.remove('d-none');
       } else {
           el.classList.add('d-none');
       }
    });
};

let searchInput = document.getElementById("search_task");
searchInput.addEventListener("search", filter);
searchInput.addEventListener("keyup", filter);
