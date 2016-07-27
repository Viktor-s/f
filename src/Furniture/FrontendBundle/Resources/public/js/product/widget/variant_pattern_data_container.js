var variant_pattern_data_container = function (patterns) {

    var options = {
        patterns: patterns,
        filtered: patterns,
        found: null,
        filters: {},
    };

    $this = this;

    return {
        refreshFilters: function(){
            this.setFilters({});
        },
        
        getAll: function () {
            return options.patterns;
        },
        
        getFiltered: function () {
            return options.filtered;
        },
        
        setFilters: function (filters) {
            //console.log(filters);
            options.filtered = [];
            options.filters = filters;  
            options.found = null;
            options.patterns.forEach(function (el) {
                var ok = true;
                $.each(options.filters, function (index, value) {
                    if (!el.options[index] || $.inArray(value, el.options[index]) < 0 ) {
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
        
        getFilteredWithFilterValue: function(filter, value){
            var res = [];
            options.filtered.forEach(function (el) {
                if(el.options[filter] && $.inArray(value, el.options[filter]) > -1){
                    res.push(el);
                }
            });
            return res;
        },
        
        getFilters: function () {
            return options.filters;
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
        
        isFilteredItemFound: function(){
            return this.getIfOneItemWithFilterFound() ? true : false;
        }
    };

}