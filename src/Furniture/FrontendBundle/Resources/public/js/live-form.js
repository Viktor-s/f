;(function ($){
    "use strict";

    function parseUrlQuery()
    {
        var url = window.location.href,
            urlParts = url.split('?');

        if (urlParts.length == 1) {
            urlParts.push('');
        }

        var queryString = urlParts[1],
            queryParts = queryString.split('&'),
            query = {},
            item, itemParts, itemName, itemValue, itemMultiple,
            i;

        for (i in queryParts) {
            itemMultiple = false;

            if (queryParts.hasOwnProperty(i)) {
                item = queryParts[i];

                itemParts = item.split('=');

                if (itemParts.length == 1){
                    itemParts.push('');
                }

                itemName = decodeURIComponent(itemParts[0]);
                itemValue = itemParts[1];

                if (itemName.substr(itemName.length - 2) == '[]') {
                    itemName = itemName.substr(0, itemName.length - 2);
                    itemMultiple = true;
                }

                var _matchParts = /\[\d+\]/.exec(itemName);

                if (_matchParts && _matchParts.length > 0) {
                    itemName = itemName.substr(0, _matchParts.index);
                    itemMultiple = true;
                }

                if (itemMultiple) {
                    if (!query.hasOwnProperty(itemName)) {
                        query[itemName] = [itemValue];
                    } else {
                        if ($.isArray(query[itemName])) {
                            query[itemName].push(itemValue);
                        } else {
                            query[itemName] = [itemValue];
                        }
                    }
                } else {
                    query[itemName] = itemValue;
                }
            }
        }

        return query;
    }

    function liveForm(element, event, options)
    {
        var name = element.attr('name'),
            isMultiple = name.substr(name.length - 2) == '[]',
            checkbox = element.is('input[type="checkbox"]');

        options = $.extend({
            multiple: isMultiple
        }, options);

        if (isMultiple && name.substr(name.length - 2) == '[]') {
            name = name.substr(0, name.length - 2);
        }

        element.on(event, function () {
            var
                query = parseUrlQuery(),
                val = $(this).val(),
                index;

            if (options.multiple) {
                if (checkbox) {
                    if ($(this).is(':checked')) {
                        if (query.hasOwnProperty(name)) {
                            if ($.isArray(query[name])) {
                                if (query[name].indexOf(val) === -1) {
                                    query[name].push(val);
                                }
                            } else {
                                query[name] = [val];
                            }
                        } else {
                            query[name] = [val];
                        }
                    } else {
                        if (query.hasOwnProperty(name)) {
                            if ($.isArray(query[name])) {
                                index = query[name].indexOf(val);

                                if (index > -1) {
                                    if (query[name].length == 1) {
                                        delete query[name];
                                    } else {
                                        query[name] = query[name].splice(index, 1);
                                    }
                                }
                            } else {
                                delete query[name];
                            }
                        }
                    }
                }
            } else {
                query[name] = val;
            }

            window.location = window.location.origin + window.location.pathname + '?' + $.param(query);
        });
    }

    $.fn.liveForm = function ()
    {
        $(this).each(function () {
            var element = $(this);

            if (element.is('select')) {
                liveForm(element, 'change', {});
            } else if (element.is('input[type="checkbox"]')) {
                liveForm(element, 'change', {});
            }
        });
    };

    $(document).ready(function () {
        $(document).find('[live-form]').liveForm();
    });
})(jQuery);