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
    labels: ['1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '9h', '9h', '9h', '9h', '9h', '9h',  '9h', '9h', '9h',  '9h', '9h', '9h', '9h', '9h', '9h', '9h', '9h', '9h', '9h', '9h', '9h'],
    // Naming the series with the series object array notation
    series: [
        {
            name: 'series-1',
            data: [15, 15, 13, 13, 11, 13, 13, 13, 13, 15, 15, 15, 16, 16, 16, 15, 10, 10, 10, 10, 10, 9, 9, 9, 9, 10, 10, 10, 10, 10,]
        },
        {
            name: 'series-2',
            data: [13, 12, 14, 12, 10, 13, 12, 10, 12, 12, 10, 12, 15, 15, 13, 12, 15, 13, 12, 10, 12, 15, 10, 12, 15, 10, 12, 15, 12, 15,]
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
            showArea: true,
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
            threshold: 'series-2',// [12, 10, 12, 17, 10, 13, 12, 13, 15, 14, 12, 10, 10, 10, 11, 13, 12, 14, 14, 10, 12, 15, 10, 12, 15, 10, 12, 15, 12, 15,],
            lineSmooth: Chartist.Interpolation.step()
        }),
        Chartist.plugins.ctThreshold({
            threshold: 'series-1',// [12, 10, 12, 17, 10, 13, 12, 13, 15, 14, 12, 10, 10, 10, 11, 13, 12, 14, 14, 10, 12, 15, 10, 12, 15, 10, 12, 15, 12, 15,],
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