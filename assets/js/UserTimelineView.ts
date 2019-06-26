import { View, createPlugin } from '@fullcalendar/core';
import { ResourceTimelineView } from '@fullcalendar/resource-timeline';

class UserTimelineView extends ResourceTimelineView {


    initialize() {
        // called once when the view is instantiated, when the user switches to the view.
        // initialize member variables or do other setup tasks.
    }

    buildPositionCaches() {
    }
}

export default createPlugin({
    views: {
        userTimeline: UserTimelineView
    }
});