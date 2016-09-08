;(function ($) {
    $(document).ready(function(){
        $( document ).on( 'click', '.add-entity-widget .add-entity-widget-delete', function(){
            var el = $(this);
            el.parent().parent().remove();
            return false;
        });

        var initialize = function() {
            var
              params = {},
              extraElements = $('.autocomplete-extra-params');

            $.each(extraElements, function() {
                if ($(this).data('extra-param-name') && $(this).data('parent-widget')) {
                    params[$(this).data('parent-widget')] = {};
                    params[$(this).data('parent-widget')][$(this).data('extra-param-name')] = $(this).attr('id');
                }
            });

            $('.add-entity-widget').each(function(id, el){
                el = $(el);
                var
                  input_name = el.attr('fullname'),
                  multiple = !!el.attr('multiple'),
                  list_table_body = el.parent().find('tbody'),
                  source = el.attr('source'),
                  autocompleteEle = el.find('.add-entity-widget-name'),
                  widgetName = autocompleteEle.data('widget-name');

                if (params.hasOwnProperty(widgetName)) {
                    var queryParams = {};
                    for (var i in params[widgetName]) {
                        if (params[widgetName].hasOwnProperty(i)) {
                            queryParams[i] = $('#' + params[widgetName][i]).val();
                        }
                    }

                    source += '?' + $.param(queryParams);
                }

                autocompleteEle.autocomplete({
                    source: source,
                    select: function( event, ui ) {
                        var item = ui.item;

                        if (!multiple) {
                            list_table_body.find('tr').remove()
                        }

                        list_table_body.append('\
                                    <tr>\
                                        <td>'+item.value+'</td>\
                                        <td>'+item.label+'</td>\
                                        <td><input name="'+input_name+'" type="hidden" value="'+item.value+'">\
                                            <a class="btn btn-default add-entity-widget-delete" href="#" role="button">Remove</a>\
                                        </td>\
                                    </tr>\
                    ');
                        $(this).val('');
                        return false;
                    }
                });
            });

            $.each(extraElements, function() {
               $(this).on('change', function(e) {
                   var queryParams = {};
                   for (var i in params) {
                       if (params.hasOwnProperty(i)) {
                           queryParams[i] = {};
                           for (var j in params[i]) {
                               if (params[i].hasOwnProperty(j)) {
                                   var value = $('#' + params[i][j]).val();
                                   if (value) {
                                       queryParams[i][j] = value;
                                   }
                               }
                           }
                       }
                   }
                   for (var i in queryParams) {
                       if (queryParams.hasOwnProperty(i)) {
                           var
                             autoCompleteEle = $('[data-widget-name="' + i + '"]'),
                             source = $(autoCompleteEle.parents('.add-entity-widget')[0]).attr('source');

                           source += '?' + $.param(queryParams[i]);
                           autoCompleteEle.autocomplete('option', 'source', source);
                       }
                   }
               })
            });
        };

        initialize();

        $(document).on('collection-form-add', function(e) {
            initialize();
        });

        $('.backend-image').each(function () {
            var
                inputPath = $(this).find('.path-input input[type="hidden"]'),
                imageContainer = $(this).find('.image-container'),
                $this = $(this);

            $(this).find('.backend-image-delete').click(function () {
                inputPath.val('');
                $this.removeClass('path-exist');
                imageContainer.remove();
            });
        });
    });
})(jQuery);
