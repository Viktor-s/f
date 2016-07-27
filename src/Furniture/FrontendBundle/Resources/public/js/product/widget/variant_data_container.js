var variant_data_container = function (variants) {

    var options = {
        variants: variants,
        filtered: variants,
        found: null,
        filters: {},
    };

    $this = this;

    return {
        refreshFilters: function(){
            this.setFilters({});
        },
        
        getAll: function () {
            return options.variants;
        },
        
        getFiltered: function () {
            return options.filtered;
        },
        
        setFilters: function (filters) {
            //console.log(filters);
            options.filtered = [];
            options.filters = filters;   
            options.found = null;
            options.variants.forEach(function (el) {
                var ok = true;
                $.each(options.filters, function (index, value) {
                    if (!el.options[index] || el.options[index] != value) {
                        ok = false;
                        return ok;
                    }

                });
                if (ok){
                    options.filtered.push(el);
                }
            });
            $(document).trigger("filter:update");
            return this;
        },
        
        getIfOneItemWithFilterFound: function(){
            /* if already cached */
            if(options.found)
                return options.found;
            
            
            var found = null;
            options.filtered.forEach(function (el) {
                if( Object.keys(el.options).length == Object.keys(options.filters).length ){
                    if(!found){
                        found = el;
                    }else{
                        found = null;
                        return false;
                    }
                }
            });
            
            options.found = found;
            
            return options.found;
        },
        
        getFilteredWithFilterValue: function(filter, value){
            var res = [];
            options.filtered.forEach(function (el) {
                if(el.options[filter] && el.options[filter] == value){
                    res.push(el);
                }
            });
            return res;
        },
        
        getFilters: function () {
            return options.filters;
        },
        
        isFilteredItemFound: function(){
            return this.getIfOneItemWithFilterFound() ? true : false;
        }
    };

};
