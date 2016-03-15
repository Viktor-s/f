;(function ($) {
    "use strict";

    var
        jQueryAjax = $.ajax,
        controlUnauthorizedError = function (e)
        {
            if (e.status == 401) {
                window.location = '/';

                return false;
            } else if (e.status == 403) {
                window.location = '/';
            }
        };

    /**
     * Override base jQuery ajax for custom control errors
     *
     * @param {Object} properties
     */
    $.ajax = function (properties)
    {
        var error;

        if (!properties.hasOwnProperty('error')) {
            error = function () {};
        } else {
            error = properties.error;
        }

        properties.error = function ()
        {
            var status = controlUnauthorizedError.apply(null, arguments);

            if (status === false) {
                return;
            }

            error.apply(null, arguments);
        };

        return jQueryAjax(properties);
    }
})(jQuery);