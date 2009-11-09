// behavior.js - by Dave Herman
// Copyright (C) 2005 by Dave Herman
//
// Based on behaviour.js by Ben Nolan, June 2005
// and getElementBySelector.js by Simon Willison, 2004.
//
// This library is free software; you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 2.1 of the License, or (at
// your option) any later version.
//
// This library is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
// License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this library; if not, write to the Free Software Foundation,
// Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

// =============================================================================
// Class: HandlerSet
// =============================================================================

function HandlerSet() {
    this.clearHandlers();
}

HandlerSet.prototype = {
    addHandler : function(f, key) {
        key = key || this.uniqueID++;
        this.installedHandlers[key] = f;
        return key;
    },

    removeHandler : function(key) {
        delete this.installedHandlers[key];
    },

    clearHandlers : function() {
        this.installedHandlers = { };
        this.uniqueID = 0;
    },

    replaceHandlers : function(f, key) {
        clearHandlers();
        return addHandler(f, key);
    },

    applyAll : function(object, arguments) {
        for (var handler in this.installedHandlers) {
            this.installedHandlers[handler].apply(object, arguments);
        }
    },

    debug : function() {
        var str = "";
        for (var p in this.installedHandlers) {
            str += p + " => " + this.installedHandlers[p] + "\n";
        }
        alert(str);
    }
};

// =============================================================================
// Function Class: EventHandler
// =============================================================================

function isEventHandler(x) {
    return (typeof x == 'function' &&
            x.handlers &&
            x.handlers.constructor == HandlerSet);
}

function makeEventHandler(original) {
    var handlers = new HandlerSet();

    if (typeof original == 'function') {
        handlers.addHandler(original);
    }

    // The event handler is a function, so it can be used with the DOM.
    // But when it's called, we apply all the handlers in the set.
    var result = function() {
        handlers.applyAll(this, arguments);
    };

    // We also expose its handler set so we can get at it later.
    result.handlers = handlers;

    return result;
}

// =============================================================================
// Module: Behavior
// =============================================================================

var Behavior = {
    registry : new Array,

    register : function(sheet) {
        Behavior.registry.push(sheet);
    },

    registerEventHandlers : function(element, handlers) {
        for (var event in handlers) {
            if (!isEventHandler(element[event])) {
                element[event] = makeEventHandler(element[event]);
            }
            element[event].handlers.addHandler(handlers[event]);
        }
    },

    apply : function() {
        for (var i = 0; i < Behavior.registry.length; i++) {
            var sheet = Behavior.registry[i];
            for (var selector in sheet) {
                var list = document.getElementsBySelector(selector);
                if (!list) {
                    continue;
                }
                for (var j = 0; j < list.length; j++) {
                    Behavior.registerEventHandlers(list[j], sheet[selector]);
                }
            }
        }
    },

    addLoadHandler : function(handler) {
        var oldHandler = window.onload;

        if (typeof oldHandler != 'function') {
            window.onload = handler;
        }
        else {
            window.onload = function() {
                oldHandler();
                handler();
            };
        }
    }
};

Behavior.addLoadHandler(function() { Behavior.apply(); });
