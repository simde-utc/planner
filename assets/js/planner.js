
import { Calendar } from '@fullcalendar/core';
import timelinePlugin from '@fullcalendar/timeline'
import common from '@fullcalendar/resource-common/main'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import bootstrap from "@fullcalendar/bootstrap";
import interactionPlugin, {Draggable} from '@fullcalendar/interaction';
import userTimelinePlugin from './UserTimelineView';

import '@fullcalendar/core/main.css';
import '@fullcalendar/timeline/main.css';
import '@fullcalendar/resource-timeline/main.css';
import '../css/planner.scss';


document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('planner');
    let draggableEl = document.getElementById('draggable');

    let calendar = new Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        plugins: [ resourceTimelinePlugin, bootstrap, interactionPlugin, userTimelinePlugin ],
        themeSystem: 'bootstrap',
        editable: true,
        selectable: true,
        droppable: true,
        select: function() {
            console.log('ok');
        },
        unselect: function() {
            console.log('ok 2');
        },
        bootstrapFontAwesome : {
            close: 'times',
            prev: 'chevron-left',
            next: 'chevron-right',
            prevYear: 'angle-double-left',
            nextYear: 'angle-double-right'
        },
        resourceAreaWidth: "20%",
        resourceGroupText: "Permanencier",
        customButtons: {
            eventStart: {
                text: "Début de l'évenement",
                click: function() {
                    calendarEl.fullCalendar('gotoDate', '{{ event.startDate|date("Y-m-d H:i:s") }}');
                }
            }
        },
        header: {
            left:   'title',
            center: '',
            right:  'eventStart today prev,next'
        },
        locale: 'fr',
        resources: calendarEl.dataset.usersUrl,
        events: calendarEl.dataset.tasksUrl,
        nowIndicator: true,
        defaultView: 'event',
        views: {
            event: {
                type: 'userTimeline',
                duration: { hours: 25 },
                slotDuration: '00:15',
                slotWidth: 20,
            }
        },
    });

    calendar.render();

    new Draggable(draggableEl, {
        itemSelector: '.event-draggable',
        eventData: function(eventEl) {
            return {
                title: eventEl.dataset.title,
                duration: eventEl.dataset.duration
            };
        }
    });
});