;(function ($, document) {
    "use strict";

    var _lazyContainer;

    /**
     * Lazy load image
     *
     * @param {HTMLElement} image
     *
     * @constructor
     */
    window.imageLazyLoad = function ImageLazyLoad(image)
    {
        // Get source
        var
            _loaded = false,
            _processed = false,
            _source = image.getAttribute('data-src'),
            _createLazyImage = function ()
            {
                // Clone image
                var e = image.cloneNode(false);
                e.removeAttribute('data-src');
                e.src = _source;

                return e;
            },
            _createLazyContainer = function ()
            {
                if (_lazyContainer) {
                    return _lazyContainer;
                }

                _lazyContainer = document.createElement('div');
                _lazyContainer.style.position = 'absolute';
                _lazyContainer.style.top = '-99999px';
                _lazyContainer.style.left = '-99999px';

                document.getElementsByTagName('body')[0].appendChild(_lazyContainer);

                return _lazyContainer;
            };

        /**
         * Load process
         */
        this.load = function ()
        {
            if (_processed || _loaded) {
                return;
            }

            _processed = true;

            var
                lazyImage = _createLazyImage(),
                lazyContainer = _createLazyContainer();

            lazyImage.onload = function () {
                // Replace "src" attribute. Image have been loaded.
                image.setAttribute('src', _source);
                _loaded = true;
                _processed = false;
                lazyImage.parentNode.removeChild(lazyImage);
            };

            lazyImage.onerror = function ()
            {
                _loaded = true;
                _processed = false;
                lazyImage.parentNode.removeChild(lazyImage);

                console.warn('The image with path "' + _source + '" can not be load.');
            };

            lazyContainer.appendChild(lazyImage);
        }
    }

    $(document).ready(function () {
        $('img[data-src]').each(function () {
            var dataSource = $(this).attr('data-src');

            if (dataSource.indexOf('holder.js') === 0) {
                // This image processed with Holder.js. We can not process this image
                return;
            }

            (new imageLazyLoad(this)).load();
        });
    });
})(jQuery, document);