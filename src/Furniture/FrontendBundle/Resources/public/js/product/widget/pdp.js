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
         
        if(element.is('input:hidden')){
            var selectedInput = element.data('input-id');
            var selectedVariant = element.data('input-variant');
            
            $(document).on('filter:update', function (event) {
                var filters = data_container.getFilters();
                
                if(element.parents('div').css('display') == 'none'){
                    if(filters[selectedInput]){
                        delete filters[selectedInput];
                        data_container.setFilters(filters);
                    }
                }else{
                    if(!filters[selectedInput]){
                        filters[selectedInput] = selectedVariant;
                        data_container.setFilters(filters);
                    }
                }
                
            });
        }else{
            element.change(function(e){
                var el = $(this);
                var selectedInput = el.parents('.simple-drop-down.simple-field').first().data('input-id');
                var selectedVariant = el.find(":selected").data('input-variant');
                var filters = data_container.getFilters();

                filters[selectedInput] = selectedVariant;
                data_container.setFilters(filters);
            });

            // Click on material if it is only one in inline.
            if (element.is(':visible') && element.find('option').length == 1) {
                element.prop('selectedIndex', 0).change();
            }

            $(document).on('filter:update', function (event) {
                var selectInput = element.parent('.simple-drop-down.simple-field').data('input-id');

                if( !data_container.getFilters()[selectInput] ){
                    element.prop('selectedIndex', -1);
                }
                else {
                    element
                        .find('option[data-input-variant="'+data_container.getFilters()[selectInput]+'"]')
                        .prop('selected', 'selected');
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

        // Click on material if it is only one in inline.
        if (element.is(':visible')) {
            var materials = element.find('.entry');
            if (materials.length == 1) {
                $(materials).trigger('click');
            }
        }
         
        $(document).on('filter:update', function (event) {
            var selectInput = element.data('input-id');
            if( !data_container.getFilters()[selectInput] ){
                element.find('.entry.active').removeClass('active');
            }
            else {
                element.find('.entry[data-input-variant="'+ data_container.getFilters()[selectInput] +'"]').addClass('active');
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
                var cloneImage = item.find('img').clone();
                if (typeof imageLazyLoad === 'function'
                    && typeof cloneImage.data('src') !== undefined) {
                    (new imageLazyLoad(cloneImage.get(0))).load()
                }

                this.showSelected.find('img').replaceWith(cloneImage);
                //Replace label
                this.showSelected.find('.caption').text(item.find('.caption').text());
                // Unavailable materials logic.
                var unavailable = item.find('.variant-unavailable');
                if (unavailable.length > 0) {
                    this.showSelected.addClass('unavailable');
                    this.showSelected.find('.variant-unavailable').removeClass('hidden');
                }
                else {
                    this.showSelected.removeClass('unavailable');
                    this.showSelected.find('.variant-unavailable').addClass('hidden');
                }
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
             if (el.data('available')) {
                 $(this).parent().find('.active').removeClass('active');
                 el.addClass('active');
                 selectedBtn.select(el);
             }
        });
         
        element.find('.material-entry[data-input-variant]').click(function(){
            var el = $(this);
            if (el.data('available')) {
                var selectedInput = element.data('input-id');
                var selectedVariant = el.data('input-variant');
                filters = data_container.getFilters();
                filters[selectedInput] = selectedVariant;
                data_container.setFilters(filters);
                $('#' + opener.data('popup-id')).removeClass('active');
                setTimeout(function () {
                    $('#' + opener.data('popup-id')).removeClass('visible');
                }, 500);
            }
        });
         
        $(document).on('pdp:change', function(event){
            if (element.is(':visible')) {
                var popupMaterials = element.find('.material-entry[data-input-variant]');
                if (popupMaterials.length == 1) {
                    $(popupMaterials).trigger('click');
                }
            }else{
                
            }
        })
         
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
