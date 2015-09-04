$(document).ready(function(){
    $( document ).on( 'click', '.add-entity-widget .add-entity-widget-delete', function(){
        var el = $(this);
        el.parent().parent().remove();
        return false;
    });
    
    $('.add-entity-widget').each(function(id, el){
        el = $(el);
        var input_name = el.attr('fullname');
        var list_table_body = el.parent().find('tbody');
        el.find('.add-entity-widget-name').autocomplete({
            source: el.attr('source'),
            select: function( event, ui ) {
                var item = ui.item;
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
    })
});