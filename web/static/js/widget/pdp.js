$(document).ready(function () {
    $('[data-input-container="default-select"]').change(function(e){
        
    });
});

$.widget('furniture.pdp_default_select', {
    options: {
        data_container: {},
    },
     _create: function () {
        
         var data_container = this.options.data_container;
         var element = this.element;
         element.prop('selectedIndex', -1);
         element.change(function(e){
            var el = $(this);
            var selectedInput = el.data('input-id');
            var selectedVariant = el.find(":selected").data('input-variant');
            var filters = {};
            if( data_container.getFilteredWithFilterValue(selectedInput, selectedVariant).length > 0 ){
                var filters = data_container.getFilters();
            }
            filters[selectedInput] = selectedVariant;
            data_container.setFilters(filters);
        });
        
        $(document).on('filter:update', function (event) {
            var selectInput = element.data('input-id');
            if( !data_container.getFilters()[selectInput] ){
                element.prop('selectedIndex', -1);
            }
            element.find("option").each(function(){
                var selectVariant = $(this).data('input-variant');
                if(data_container.getFilteredWithFilterValue(selectInput, selectVariant).length == 0){
                    $(this).css('background','#E0E0E0');
                }else{
                    $(this).css('background','white');
                }
            });
        });
        
     }
});

$.widget('furniture.pdp_inline_select', {
    options: {
        data_container: {},
    },
     _create: function () {
         var data_container = this.options.data_container;
         var element = this.element;
         
         element.find('.entry').click(function(){
            var el = $(this);
            var selectedInput = element.data('input-id');
            var selectedVariant = el.data('input-variant');
            filters = {};
            if( data_container.getFilteredWithFilterValue(selectedInput, selectedVariant).length > 0 ){
                var filters = data_container.getFilters();
            }
            filters[selectedInput] = selectedVariant;
            data_container.setFilters(filters);
         });
         
         $(document).on('filter:update', function (event) {
            var selectInput = element.data('input-id');
            if( !data_container.getFilters()[selectInput] ){
                element.find('.entry.active').removeClass('active');
            }
            
            element.find(".entry").each(function(){
                var selectVariant = $(this).data('input-variant');
                if(data_container.getFilteredWithFilterValue(selectInput, selectVariant).length == 0){
                    $(this).css('background','#E0E0E0');
                }else{
                    $(this).css('background','white');
                }
            });
        });
         
     }
 });
 
 $.widget('furniture.pdp_popup_select', {
    options: {
        data_container: {},
    },
     _create: function () {
         var data_container = this.options.data_container;
         var element = this.element;
         
         element.find('.open-input').click(function(){
            var opener = $(this);
            $('#'+opener.data('popup-id')).addClass('visible active');;
            initSwiper();
            return false;
         });
        
         element.find('.material-selector .material-entry').on('click', function () {
            var el = $(this);
            $(this).parent().find('.active').removeClass('active');
            el.addClass('active');
        });
         
        element.find('.material-entry').click(function(){
            var el = $(this);
            var selectedInput = element.data('input-id');
            var selectedVariant = el.data('input-variant');
            filters = {};
            if( data_container.getFilteredWithFilterValue(selectedInput, selectedVariant).length > 0 ){
                var filters = data_container.getFilters();
            }
            filters[selectedInput] = selectedVariant;
            data_container.setFilters(filters);
         });
         
         $(document).on('filter:update', function (event) {
            var selectInput = element.data('input-id');
            if( !data_container.getFilters()[selectInput] ){
                element.find('.material-entry.active').removeClass('active');
            }
            element.find(".material-entry").each(function(){
                var selectVariant = $(this).data('input-variant');
                if(data_container.getFilteredWithFilterValue(selectInput, selectVariant).length == 0){
                    $(this).css('background','#E0E0E0');
                }else{
                    $(this).css('background','white');
                }
            });
        });
         
     }
 });