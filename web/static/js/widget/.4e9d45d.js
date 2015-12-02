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
        }
    };

}
$.widget('furniture.variant_buy_btn', {
    options: {
        data_container: {},
        on_buy_click: function(){}
    },
    _inputs: {},
    _btn_container: {},
    _create: function () {
        var $this = this;
        this._bind();
        $this.refresh();
        $(document).on('filter:update', function (event) {
            $this.refresh();
        });
    },
    _bind: function(){
        var $this = this;
        //On buy btn click event
        this.element.click(this.options.on_buy_click);
    },
    refresh: function(){
        //console.log(this.options.data_container.getFiltered());
        //console.log(this.element);
        if(this.options.data_container.getFiltered().length == 1){
            this.element.prop('disabled', false);
        }else{
            this.element.prop('disabled', true);
        }
    }
});

