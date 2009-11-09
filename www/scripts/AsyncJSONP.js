/*
 * Author - Yuichi Tateno aka secondlife <hotchpotch@gmail.com> http://rails2u.com/
 *
 * This program is dual-licensed free software
 * you can redistribute it and/or modify it under the terms of the MIT License or the Academic Free License v2.1.
 */

try {
    if (typeof(MochiKit.Async) == 'undefined') {
        throw "";
    }
} catch (e) {
    throw "MochiKit.Async JSONP depends on MochiKit.Async!";
}

if (typeof(MochiKit.Async.JSONPCallbacks) == 'undefined') {
    MochiKit.Async.JSONPCallbacks = {
            callbackId: MochiKit.Base.counter()
    };
}

MochiKit.Base.update(MochiKit.Async, {
    sendJSONPRequest: function (url, callback_query, timeout/* = 30 */, _options/* optional */) {
        var m = MochiKit.Base;
        var self = MochiKit.Async;
        var callbackId = '_' + self.JSONPCallbacks.callbackId();

        if (typeof(timeout) == "undefined" || timeout === null) {
            timeout = 30;
        }
        var options = {
            'type': 'text/javascript',
            'className': 'JSONPRequest'
        };
        m.update(options, _options || {});

        if(url.indexOf('?') >= 0) {
            var ary = url.split('?', 2);
            url = ary[0];
            var queryParams = m.parseQueryString(ary[1] || '');
        } else {
            var queryParams = {};
        }
        queryParams[callback_query] = 'MochiKit.Async.JSONPCallbacks.' + callbackId;
        url += '?' + m.queryString(queryParams);

        var d = new self.Deferred();
        self.JSONPCallbacks[callbackId] = partial(self._jsonp_callback_handler, d);

        var script = document.createElement('script');
        m.update(script, options);
        m.update(script, {
            'src': url,
            'id': '_JSONPRequest' + callbackId
        });

        // FIXME don't work opera.
        // setTimeout with appendChild(script) don't ASYNC timer...
        var timeout = setTimeout(
            function() {
                d.canceller();
                d.errback('JSONP Request timeout');
            }, Math.floor(timeout * 1000)
        );
        d.canceller = m.partial(self._jsonp_canceller, callbackId, timeout);
        
        setTimeout(function() {
            document.getElementsByTagName('head')[0].appendChild(script);
        }, 1); // for opera

        return d;
    },
    
    _jsonp_callback_handler: function(d, json) {
        d.canceller(); // remove script element and clear timeout
        d.callback(json);
    },

    _jsonp_canceller: function(callbackId, timeout) {
        try {
            clearTimeout(timeout);
        } catch (e) {
            // pass
        }
        try {
            /* remove script element */
            var element = document.getElementById('_JSONPRequest' + callbackId);
            element.parentNode.removeChild(element);
        } catch (e) {
            // pass
        }
        MochiKit.Async.JSONPCallbacks[callbackId] = function() {};
    }
});

if (MochiKit.__export__) {
    this['sendJSONPRequest'] = MochiKit.Async.sendJSONPRequest;
}

