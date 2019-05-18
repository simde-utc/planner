/**
 * Chartist.js plugin that enables drag & drop of line chart
 * points as a means of updating the underlying data.
 *
 * Copyright (c) 2016-2017 Amichai Rothman
 * Licensed under the MIT License.
 */
/* global Chartist */
(function (window, document, Chartist) {
    'use strict';

    var defaultOptions = {
        appendToBody: false, // whether marker element should be appended to document body or chart
        attribute: 'ct:indices', // name of element attribute used to store data indices
        axis: 'y', // axis on which changes can be made ('x', 'y' or 'xy')
        pointClass: 'ct-point', // class used as selector for points
        updateWhileDragging: true, // update element's ct:value attribute and simulate mouseout/over
                                   // events while dragging so e.g. tooltip plugin will be updated
        updateSegments: false, // move adjacent line segments when updating a point
        updateCallback: undefined, // callback invoked when data is updated

        // the following classes are added to draggable elements under various conditions
        commonClass: 'chartist-drag', // always added
        highlightClass: 'chartist-drag-highlight', // indicates an element can be dragged
        sourceClass: 'chartist-drag-source', // source element during drag
        destinationClass: 'chartist-drag-destination' // destination (moving) element during drag
    };

    Chartist.plugins = Chartist.plugins || {};

    Chartist.plugins.drag = function(options) {

        // initialize options
        options = Chartist.extend({}, defaultOptions, options);
        options.axisX = options.axisX || options.axis.toLowerCase().indexOf('x') > -1;
        options.axisY = options.axisY || options.axis.toLowerCase().indexOf('y') > -1;

        // gets or sets the regular element className string or
        // SVG element className object as an array of class names
        function classes(element, classes) {
            if (arguments.length == 1)
                return element.getAttribute('class').split(' ');
            element.setAttribute('class', classes.join(' '));
        }

        // adds/removes the given class name(s) from an element
        function toggleClass(element, enable, className) {
            var list = classes(element); // get element classes list
            for (var i = 2; i < arguments.length; i++) {
                var ind = list.indexOf(arguments[i]);
                if (enable != ind > -1) { // if not already correct
                    if (enable)
                        list.push(arguments[i]); // add class
                    else
                        list.splice(ind, 1); // remove class
                }
            }
            classes(element, list); // update element
        }

        // checks if a point is inside a rectangle
        function inRect(rect, x, y) {
            return x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
        }

        function getSeriesData(chart, seriesIndex) {
            // series is either a plain array of values (e.g. if the X axis is just
            // an array of labels rather than values), or an object containing a data property
            var series = chart.data.series[seriesIndex];
            return series.data || series;
        }

        // gets/sets the underlying data for a point on the chart
        function pointData(chart, point, data) {
            var indices = point.getAttribute(options.attribute).split(',');
            var series = getSeriesData(chart, indices[0]);
            return arguments.length > 2 ? (series[indices[1]] = data) : series[indices[1]];
        }

        // updates entire line segments along with updated point
        function updateSegments(data) {
            var series = getSeriesData(data.chart, data.seriesIndex);
            if (series.length < 3)
                return; // there are no segments
            var slopeNormalizer = data.converter.ratioX / data.converter.ratioY;
            var slope = function(i) { return slopeNormalizer *
                (series[i].y - series[i - 1].y) / (series[i].x - series[i - 1].x); };
            var sameSegment = function(s1, s2) { return Math.abs(s1 - s2) < 0.1; };
            var index = data.index;
            // find previous index where slope changes
            var first = index;
            var sl = slope(Math.max(first, 1));
            while (first > 0 && sameSegment(sl, slope(first)))
                first--;
            // find next index where slope changes
            var last = index + 1;
            sl = slope(Math.min(last, series.length - 1));
            while (last < series.length && sameSegment(sl, slope(last)))
                last++;
            last--;
            // move all points in the segments linearly
            var x = data.oldData.x;
            var dy = data.newData.y - data.oldData.y;
            var range = series[index].x - series[first].x || 1;
            for (var i = first; i < index; i++)
                series[i].y += dy * (1 - Math.abs(x - series[i].x) / range);
            range = series[last].x - series[index].x || 1;
            for (i = index; i <= last; i++)
                series[i].y += dy * (1 - Math.abs(x - series[i].x) / range);
            return { first: first, last: last };
        }

        function createConverter(axisX, axisY) {
            var rx = axisX.range || { min: 0, max: 0 }; // undefined if there is no range (just labels)
            var ry = axisY.range || { min: 0, max: 0 };
            return {
                axisX: axisX,
                axisY: axisY,
                minX: rx.min, // minimal X axis range value
                minY: ry.min, // minimal Y axis range value
                ratioX: (rx.max - rx.min) / axisX.axisLength, // X units per pixel
                ratioY: (ry.max - ry.min) / axisY.axisLength, // Y units per pixel
                convertDX: function(x) { return x * this.ratioX; }, // X pixel count to value units
                convertDY: function(y) { return y * this.ratioY; }, // Y pixel count to value units
                convertX: function(x) { return this.minX + this.convertDX(x); }, // X position to value
                convertY: function(y) { return this.minY + this.convertDY(y); } // Y position to value
            };
        }

        function createMarker() {
            return {
                element: null,
                reference: null,

                create: function(point) {
                    this.reference = point;
                    this.element = point.cloneNode(true);
                    this.element.style.pointerEvents = 'none';
                    // insert after element (i.e. before next sibling) since it determines z-order
                    point.parentNode.insertBefore(this.element, point.nextSibling);
                    var rect = point.getBoundingClientRect();
                    this.setPosition(rect.left, rect.top);
                    toggleClass(this.element, false, options.highlightClass);
                    toggleClass(this.element, true, options.commonClass, options.destinationClass);
                },

                destroy: function() {
                    if (this.element.parentNode) // if not already removed
                        this.element.parentNode.removeChild(this.element);
                },

                setPosition: function(x, y) {
                    var referenceRect = this.reference.getBoundingClientRect();
                    var dx = x - referenceRect.left;
                    var dy = y - referenceRect.top;
                    this.element.setAttribute('transform', 'translate(' + dx + ' ' + dy + ')');
                }
            };
        }

        // returns the valid drag-drop rectangle (anything outside it will be ignored)
        function getDragRect(container) {
            var grid = container.getElementsByClassName('ct-grids');
            if (grid && grid.length > 0)
                grid = grid[0];
            return (grid || container).getBoundingClientRect();
        }

        // manages registration of event handlers
        var handlers = {
            // registered event handlers metadata objects
            handlers: [],

            // registers event handlers (arguments are passed to createHandlers)
            on: function(eventNames, element, selector, callback) {
                var created = this.createHandlers(eventNames, element, selector, callback);
                created.forEach(function(h) { h.on(); });
                this.handlers = this.handlers.concat(created);
            },

            // unregisters all event handlers
            off: function() {
                this.handlers.forEach(function(h) { h.off(); });
                this.handlers = [];
            },

            // creates event handler metadata objects for an element,
            // filtered by optional selector
            createHandlers: function(eventNames, element, selector, callback) {
                return eventNames.split(' ').map(function(name) {
                    var touch = name.indexOf('touch') > -1;
                    return {
                        name: name,
                        on: function() { element.addEventListener(this.name, this.handler); },
                        off: function() { element.removeEventListener(this.name, this.handler); },
                        handler: function(event) {
                            var info = touch ? event.changedTouches[event.changedTouches.length - 1] : event;
                            if (!selector || classes(info.target).indexOf(selector) > -1)
                                callback(event, info, touch);
                        }
                    };
                });
            }
        };

        return function drag(chart) {

            // a hack to allow the plugin creator to manually detach it (e.g. remove listeners),
            // until Chartist provides a standard way of doing it
            if (arguments[1] === 'detach') {
                return handlers.off();
            }

            // currently only line charts are supported
            if (!(chart instanceof Chartist.Line))
                return;

            var container = chart.container; // chart container element
            var pointSelector = options.pointClass; // class selector string for draggable points
            var marker = createMarker(); // marker object instance
            var converter = {}; // converter (coordinates to values) instance
            var highlighted; // the currently highlighted element, or null
            var dragged; // the currently dragged element, or null
            var offset; // the offset of the dragging pointer from the top-left of dragged element

            // gets the delta between the dragged element origin and destination, converted to value
            function calcDelta() {
                var draggedRect = dragged.getBoundingClientRect();
                var markerRect = marker.element.getBoundingClientRect();
                var dx = markerRect.left - draggedRect.left; // increase towards right
                var dy = draggedRect.top - markerRect.top; // increase towards top
                var data = pointData(chart, dragged);
                // return data and context for use in updateCallback
                return {
                    oldData: data,
                    newData: data.x === undefined // either scalar value or object with x,y
                        ? data + converter.convertDY(dy)
                        : Chartist.extend({}, data, {
                            x: data.x + converter.convertDX(dx),
                            y: data.y + converter.convertDY(dy)
                        }),
                    changed: dx !== 0 || dy !== 0,
                    converter: converter,
                    dx: dx,
                    dy: dy
                };
            }

            // initialize data when points are first drawn
            chart.on('draw', function(data) {
                if (data.type === 'point') {
                    // save data series/point indices in attribute so we can find them later
                    var attributes = {};
                    attributes[options.attribute] = data.seriesIndex + ',' + data.index;
                    data.element.attr(attributes);
                    // update converter with axis physical/logical sizes if necessary
                    if (data.axisX !== converter.axisX || data.axisY !== converter.axisY)
                        converter = createConverter(data.axisX, data.axisY);
                }
            });

            // show marker on potential drag point
            handlers.on('mouseover', container, pointSelector, function(event) {
                if (dragged)
                    return;
                highlighted = event.target;
                toggleClass(highlighted, true, options.commonClass, options.highlightClass);
            });

            // hide marker on potential drag point
            handlers.on('mouseout', container, pointSelector, function(event) {
                if (dragged)
                    return;
                highlighted = null;
                toggleClass(event.target, false, options.commonClass, options.highlightClass);
            });

            // disable text selection in chart (which conflicts with dragging)
            handlers.on('mousedown', container, null, function(event) {
                event.preventDefault();
                return false;
            });

            // start drag
            handlers.on('mousedown touchstart', container, pointSelector, function(event, info, touch) {
                if (!event.button) { // only left-click, or event.button prop not supported
                    if (touch)
                        event.preventDefault(); // prevent equivalent mouse events
                    dragged = event.target;
                    var draggedRect = dragged.getBoundingClientRect();
                    offset = {
                        x: draggedRect.left - info.clientX,
                        y: draggedRect.top - info.clientY
                    };
                    marker.create(dragged);
                    toggleClass(dragged, false, options.highlightClass);
                    toggleClass(dragged, true, options.commonClass, options.sourceClass);
                }
            });

            // end drag
            handlers.on('mouseup touchend', document, null, function(event, info) {
                if (!dragged)
                    return;
                // if dropped inside chart then update, otherwise ignore
                if (inRect(getDragRect(container), info.clientX, info.clientY)) {
                    var data = calcDelta();
                    if (data.changed) {
                        var indices = dragged.getAttribute(options.attribute).split(',');
                        data.chart = chart;
                        data.element = dragged;
                        data.seriesIndex = parseInt(indices[0]);
                        data.index = parseInt(indices[1]);
                        var preventDefault = options.updateCallback
                            && options.updateCallback(data) === false;
                        if (!preventDefault) {
                            if (options.updateSegments && !event.shiftKey) {
                                data.modifiedRange = updateSegments(data);
                            } else {
                                pointData(chart, dragged, data.newData);
                                data.modifiedRange = { first: data.index, last: data.index };
                            }
                            chart.eventEmitter.emit('drag.update', data);
                            chart.update(data.chart.data);
                        }
                    }
                    if (highlighted) // restore highlight if still on same element
                        toggleClass(highlighted, true, options.commonClass, options.highlightClass);
                }
                // clean up drag
                marker.destroy();
                toggleClass(dragged, false, options.commonClass, options.sourceClass);
                dragged = null;
            });

            // track drag movement
            handlers.on('mousemove touchmove', container, null, function(event, info, touch) {
                if (!dragged)
                    return;
                if (touch)
                    event.preventDefault(); // prevent equivalent mouse events and default scrolling
                // update marker position, restricted to configured axes
                var x = options.axisX ? info.clientX + offset.x : dragged.getBoundingClientRect().left;
                var y = options.axisY ? info.clientY + offset.y : dragged.getBoundingClientRect().top;
                marker.setPosition(x, y);
                // update data and simulate mouseout+mouseover events
                // as workaround for updating e.g. the tooltip plugin
                if (options.updateWhileDragging && !touch) {
                    var data = calcDelta();
                    var value = data.newData.x === undefined // either scalar value or object with x,y
                        ? data.newData
                        : (data.newData.x + ',' + data.newData.y);
                    marker.element.setAttribute('ct:value', value);
                    marker.element.dispatchEvent(new MouseEvent('mouseout', event));
                    marker.element.dispatchEvent(new MouseEvent('mouseover', event));
                }
            });
        };
    };
}(window, document, Chartist));