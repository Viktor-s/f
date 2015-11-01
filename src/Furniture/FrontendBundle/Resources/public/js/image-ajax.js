;(function ($) {
    "use strict";

    /**
     * Image ajax
     * @param container
     * @param options
     * @param uploaderOptions
     * @constructor
     */
    function ImageAjax(container, options, uploaderOptions)
    {
        options = $.extend({
            inputName: 'file',
            urlUpload: container.attr('data-url-upload'),
            errorFormatter: this.formatterErrors,
            errorContainer: this.getErrorsContainer,
            popoverError: this.popoverError,
            onRemove: function () { return true; },
            onStart: function () {},
            onComplete: function () {},
            onSuccess: function () {},
            onError: function () {}
        }, options);

        uploaderOptions = $.extend({
            formData: function(form) {
                return [];
            }
        }, uploaderOptions);

        this.options = options;
        this.uploaderOptions = uploaderOptions;
        this.container = container;
        this.uploader = null;
        this.popover = null;
        this.fullName = container.attr('data-full-name');

        var _init = false;

        /**
         * Is initialize ajax container
         */
        this.isInit = function()
        {
            return _init;
        };

        this._initialize();
        _init = true;
    }

    /**
     * Hide image upload
     */
    ImageAjax.prototype.hideImageUpload = function()
    {
        this.container.find('.image-upload').hide();
    };

    /**
     * Show image upload
     */
    ImageAjax.prototype.showImageUpload = function()
    {
        this.container.find('.image-upload').show();
    };

    /**
     * Hide image container
     */
    ImageAjax.prototype.hideImageContainer = function()
    {
        this.container.find('.image-container').hide();
    };

    /**
     * Show image container
     */
    ImageAjax.prototype.showImageContainer = function()
    {
        this.container.find('.image-container').show();
    };

    /**
     * View image container
     */
    ImageAjax.prototype.viewImageContainer = function()
    {
        this.showImageContainer();
        this.hideImageUpload();
    };

    /**
     * View image upload
     */
    ImageAjax.prototype.viewImageUpload = function()
    {
        this.showImageUpload();
        this.hideImageContainer();
    };

    /**
     * Set image src
     */
    ImageAjax.prototype.setImagePath = function(src)
    {
        this.container.find('.image-container img').attr('src', src);
        this.container.find('input[name="' + this.fullName + '"]').val(src);
    };

    /**
     * Remove callback
     */
    ImageAjax.prototype.removeCallback = function()
    {
        if (this.options.onRemove()) {
            this.setImagePath('');
            this.viewImageUpload();
        }

        return false;
    };

    /**
     * Set remove callback
     */
    ImageAjax.prototype.setRemoveCallback = function(e)
    {
        e.click($.proxy(this, 'removeCallback'));
    };

    /**
     * Find remove buttons
     */
    ImageAjax.prototype.findRemoveButtons = function(e)
    {
        return e.find('a[href="#remove-image"]');
    };

    /**
     * Get errors container
     */
    ImageAjax.prototype.getErrorsContainer = function()
    {
        return this.container;
    };

    /**
     * Initialize ajax container
     */
    ImageAjax.prototype._initialize = function()
    {
        if (this.isInit()) {
            return true;
        }

        if (this.container.attr('data-image-path')) {
            // Image path already exists
            this.setImagePath(this.container.attr('data-image-path'));
            this.viewImageContainer();
        } else {
            // Image path not found
            this.viewImageUpload();
        }

        if (this.container.attr('data-errors')) {
            // Initialize popovers
            this.popover = this.container.find('.image-container, .image-upload');
            this.options.popoverError.call(this, this.popover);
        }

        this.setRemoveCallback(this.findRemoveButtons(this.container));

        var id = this.fullName
            .replace(/\[/g, '_')
            .replace(/\]/g, '_');

        // Create hidden field input
        var e = document.createElement('input');
        e.type = 'file';
        e.setAttribute('id', 'image_ajax_' + id);
        e.setAttribute('style', 'visibility: hidden; position: absolute; top: -10000px; left: -10000px;');

        this.container.find('.image-upload').append(e);

        var $this = this;

        this.uploader = $('#image_ajax_' + id).fileupload({
            url: this.options.urlUpload,
            dataType: 'json',
            replaceFileInput: false,
            maxNumberOfFiles: 1,
            paramName: this.options.inputName,
            formData: this.uploaderOptions.formData,
            success: function(data) {
                if ($this.popover) {
                    $this.popover.popover('destroy');
                }
                $this.successUpload.apply($this, [data]);
            },
            error: function(d) {
                alert('Critical error with upload file. Please try again.\nResponse: ' + d.statusText);
            },
            start: function () {
                $this.options.onStart();
            }
        });

        this.container.find('.image-upload img').click(function(){
            // Open dialog
            $this.uploader.click();
        });

        return true;
    };

    /**
     * Success upload callback
     */
    ImageAjax.prototype.successUpload = function(data)
    {
        // Clear uploader value
        this.uploader.val('');

        this.options.onComplete();

        if (data.status) {
            // Success upload
            this.processSuccessUpload(data.image);
            this.options.onSuccess();
        } else {
            this.processErrorUpload(data.errors);
            this.options.onError();
        }
    };

    /**
     * Process success upload
     */
    ImageAjax.prototype.processSuccessUpload = function (src)
    {
        this.setImagePath(src);
        this.viewImageContainer();
    };

    /**
     * Process error upload
     */
    ImageAjax.prototype.processErrorUpload = function(errors)
    {
        var hash = Math.random();
        var container = this.options.errorContainer.call(this),
            message = '<div class="error-container" data-hash="' + hash + '">' +
                this.options.errorFormatter.call(this, errors) +
                '</div>';
        container.prepend(message);

        setTimeout(function(){
            $('[data-hash="' + hash + '"]').animate({
                opacity: 0
            }, 500, function(){
                $(this).remove()
            });
        }, 4000);
    };

    /**
     * Formatter error
     */
    ImageAjax.prototype.formatterErrors = function(errors)
    {
    };

    /**
     * Error popover
     */
    ImageAjax.prototype.popoverError = function(e)
    {
        e.popover({
            placement: 'top',
            trigger: 'hover',
            content: this.container.attr('data-errors'),
            html: true
        });
    };

    $(document).ready(function(){
        // Remove all temporary file inputs
        $('form').submit(function(){
            $('input[id^="image_ajax_"]').each(function(){
                $(this).remove();
            });

            return true;
        });
    });

    window.ImageAjax = ImageAjax;
})(jQuery);