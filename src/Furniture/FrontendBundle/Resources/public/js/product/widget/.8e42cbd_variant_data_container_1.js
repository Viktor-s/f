var variant_data_container = function (variants) {

    var options = {
        variants: variants,
        filtered: variants,
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
            options.filtered = [];
            options.filters = filters;

            $(this).on('filter:update', function (event) {
                //console.log('event');
            });
            
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
        getFilter: function () {
            return options.filters;
        }
    };

}