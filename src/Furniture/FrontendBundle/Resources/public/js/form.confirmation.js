;(function ($, bootbox) {
    $(document).ready(function () {
        $('form[data-confirm]').on('submit', function(e, options) {
            options = options || {};
            if ($(this).data('confirm') === true) {
                var message = $.trim($(this).data('message')).length > 0
                    ? $.trim($(this).data('message'))
                    : 'Please confirm this action!';
                if (!options.confirm) {
                    bootbox.confirm(message, function(result) {
                        if (result) {
                            $(e.currentTarget).trigger('submit', {'confirm': true});
                        }
                    });
                    return false;
                }
            }
        });
    });
})(jQuery, bootbox);