;(function ($, bootbox) {
    $(document).ready(function () {
        $('a[data-remove], a[data-confirm]').click(function () {
            var
                message = $(this).data('message'),
                href = $(this).attr('href');

            bootbox.confirm({
                title: "Confirmation required.",
                message: message,
                callback: function(result) {
                    if(result){
                        window.location = href;
                    }
                },
                buttons: {
                    'cancel': {
                        label: 'Cancel',
                        className: 'btn-primary'
                    },
                    'confirm': {
                        label: 'Ok',
                        className: 'btn-success'
                    }
                },
                backdrop: true
            });

            return false;
        });
    });
})(jQuery, bootbox);