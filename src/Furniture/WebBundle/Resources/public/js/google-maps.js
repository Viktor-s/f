;(function (window, $) {
    "use strict";

    var
        _initialized = false,
        _processed = false,
        _queue = [];

    /**
     * Initialize Google Maps API.
     *
     * @returns {*} Returns a promise
     */
    function initializeGoogleMapsApi()
    {
        if (!window.hasOwnProperty('googleMapsApiKey')) {
            throw new Error('Not found parameter "googleMapsApiKey".');
        }

        var d = $.Deferred();

        if (_initialized) {
            // The library already initialized.
            d.resolve();

            return d.promise();
        }

        if (_processed) {
            // The library now loading. Add deferred to queue.
            _queue.push(d);

            return d.promise();
        }

        _processed = true;

        // Append callback to window instance
        var callbackName = '__googleMapsInitializer';

        window[callbackName] = function () {
            d.resolve();

            _processed = false;
            _initialized = true;

            var i;

            for (i in _queue) {
                if (_queue.hasOwnProperty(i)) {
                    _queue[i].resolve();
                }
            }
        };

        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.src = '//maps.googleapis.com/maps/api/js?key=' + window.googleMapsApiKey + '&signed_in=true&libraries=places&callback=' + callbackName;
        e.async = true;

        document.getElementsByTagName('head')[0].appendChild(e);

        return d.promise();
    }

    window.initializeGoogleMaps = initializeGoogleMapsApi;
})(window, jQuery);