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
            var selectedInput = el.parents('.simple-drop-down.simple-field').first().data('input-id');
            var selectedVariant = el.find(":selected").data('input-variant');
            var filters = data_container.getFilters();
            
            filters[selectedInput] = selectedVariant;
            data_container.setFilters(filters);
        });
        
        $(document).on('filter:update', function (event) {
            var selectInput = element.parents('.simple-drop-down.simple-field').first().data('input-id');
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
            var filters = data_container.getFilters();
            
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
         
         var selectedBtn = {
             selectButton: element.find('a.button-content'),
             showSelected: element.find('a.pdp-button'),
             select: function(item){
                this.selectButton.addClass('hidden');
                this.showSelected.removeClass('hidden');
                //Replace image
                this.showSelected.find('img').attr('src', item.find('img').attr('src'));
                //Replace label
                this.showSelected.find('.caption').text(item.find('.caption').text());
             },
             unselect: function(){
                this.showSelected.addClass('hidden');
                this.selectButton.removeClass('hidden');
             }
         };
         
         var opener = element.find('a.pdp-button, a.button-content');
         opener.click(function(){
            $('#'+opener.data('popup-id')).addClass('visible active');
            return false;
         });
        
        //Select element
         element.find('.material-selector .material-entry').on('click', function () {
            var el = $(this);
            $(this).parent().find('.active').removeClass('active');
            el.addClass('active');
            selectedBtn.select(el);
        });
         
        element.find('.material-entry[data-input-variant]').click(function(){
            var el = $(this);
            var selectedInput = element.data('input-id');
            var selectedVariant = el.data('input-variant');
            filters = data_container.getFilters();
            filters[selectedInput] = selectedVariant;
            data_container.setFilters(filters);
            $('#'+opener.data('popup-id')).removeClass('active');
            setTimeout(function(){
                $('#'+opener.data('popup-id')).removeClass('visible');
            }, 500);
         });
         
         $(document).on('filter:update', function (event) {
            var selectInput = element.data('input-id');
            
            if( !data_container.getFilters()[selectInput] ){
                element.find('.material-entry.active').removeClass('active');
                selectedBtn.unselect();
            }else{
                element.find('.material-entry[data-input-variant="'+data_container.getFilters()[selectInput]+'"]').addClass('active');
            }
            element.find("[data-input-variant].material-entry").each(function(){
                var selectVariant = $(this).data('input-variant');
                var resLen = data_container.getFilteredWithFilterValue(selectInput, selectVariant).length;
                if(resLen == 0){
                    $(this).css('background','#E0E0E0');
                }else{
                    $(this).css('background','white');
                }
            });
            element.find("[data-input-variant].material-entry.active").each(function(){
                selectedBtn.select($(this));
            });
        });
         
     }
 });