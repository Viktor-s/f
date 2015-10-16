;(function ($){
    "use strict";

    function liveForm(element, event)
    {
        var name = element.attr('name');

        element.on(event, function () {
            var url = new URI(window.location),
                val = $(this).val();

            url.removeQuery(name);

            if (val) {
                url.addQuery(name, val);
            }

            window.location = url;
        });
    }

    $.fn.liveForm = function ()
    {
        $(this).each(function () {
            var element = $(this);

            if (element.is('select')) {
                liveForm(element, 'change');
            }
        });
    };

    $(document).ready(function () {
        $(document).find('[live-form]').liveForm();
    });
})(jQuery);