;(function ($, bootbox) {
    $(document).ready(function () {
        $('[data-remove]').click(function () {
            var
                message = $(this).data('message'),
                href = $(this).attr('href');

            bootbox.confirm(message, function(result) {
                if(result){
                    window.location = href;
                }
            });

            return false;
        });
    });
})(jQuery, bootbox);