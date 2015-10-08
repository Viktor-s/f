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
$.widget('furniture.variant', {
    options: {
        data_container: {},
    },
    sku_table: function (sku_table) {
        this.options.sku_table = sku_table;
    },
    _create: function () {
        var $this = this;
        this._build_inputs(this.options.data_container.getFiltered());
        this._refresh_options();
        $(document).on('filter:update', function (event) {
            $this._refresh_data_changes();
        });
    },
    _remove_inputs: function () {

        for (el in this._inputs) {
            el.remove();
        }
    },
    _table: null,
    _inputs: {},
    _refresh_data_changes: function(){
        var filters = this.options.data_container.getFilter();
        if($.isEmptyObject(filters)){
            var first = true;
            this._table.find('select').prop('disabled', true);
            this._table.find('select').prop('selectedIndex', -1);
            this._table.find('select').first().prop('disabled', false);
            return;
        }
        
        for(name in filters){
            var value = filters[name];
            this._inputs[name].find('option[value="'+filters[name]+'"]').prop('selected', true);
            this._inputs[name].find('select').prop('disabled', false);
        }
    },
    _refresh_options: function () {
        var $this = this;
        var undisabled = $this._table.find('select:not(:disabled)');
        var disabled = $this._table.find('select:disabled');
        var filters = {};
        if (undisabled.length > 0) {
            undisabled.each(function (index, el) {
                var el = $(el);
                filters[el.attr('name')] = el.val();
            });
        }
        $this.options.data_container.setFilters(filters);
        disabled.first().prop('disabled', false);
    },
    _build_inputs: function (variants) {

        this._remove_inputs();

        var $this = this;
        if (!this._table) {
            this._table = this._elements_creator._get_filter_input_table();
            this.element.append(this._table);
        }
        //Iterate variants
        variants.forEach(function (variant) {
            //Iterate varian->options
            for (option_name in variant.options) {
                var option_value = variant.options[option_name];
                if (!(option_name in $this._inputs)) {
                    $this._inputs[option_name] = $this
                            ._elements_creator
                            ._get_select_element(variant.options_labels[option_name], option_name);

                    $this._inputs[option_name].find('select').change(
                            function (e) {
                                var el = $(e.currentTarget);
                                var c_name = el.attr('name');
                                var disabled = false;
                                for (name in $this._inputs) {
                                    console.log(c_name);
                                    if (disabled) {
                                        var select = $this._inputs[name].find('select');
                                        select.prop('disabled', true);
                                        select.prop('selectedIndex', -1);
                                        continue;
                                    }
                                    disabled = (c_name == name);
                                }
                                $this._refresh_options();
                            }
                    );
                    $this._table.append($this._inputs[option_name]);
                }
                var select = $this._inputs[option_name].find('select');
                select.prop('selectedIndex', -1);
                if (select.find('option[value="' + option_value + '"]').length == 0)
                    select.append($this
                            ._elements_creator
                            ._get_option_element(option_value));
            }
            ;
        });
    },
    _elements_creator: {
        _get_filter_input_table: function () {
            return $('<table class="table"></table>');
        },
        _get_select_element: function (label, name) {
            return $('<tr><td>' + label + '</td><td>\
                                    <div class="input-group input-group-lg">\
                                        <span class="input-group-addon"></span>\
                                        <select disabled class="form-control" name="' + name + '"></select>\
                                    </div>\
                                    </td></tr>');
        },
        _get_option_element: function (value) {
            return $('<option _disabled value="' + value + '">' + value + '</option>');
        }
    },
});
$.widget('furniture.variant_table', {
    options: {
        data_container: {},
    },
    _table: {},
    _lines: {},
    _create: function () {
        var $this = this;

        $(document).on('filter:update', function (event) {
            $this._refreshVisible();
        });

        $this._build();
        $this._refreshVisible();
    },
    _build: function () {
        var $this = this;
        var table = $('<table class="table table-striped"></table>');
        var thead = $('<thead><tr></tr></thead>');
        var tbody = $('<tbody></tbody>');
        //var head_lines = [];
        this.options.data_container.getAll().forEach(function (el) {
            $this._lines[el.id] = $('<tr></tr>');
            
            for (name in el.options) {
                var val = el.options[name];
                //head_lines.push(name);
                $this._lines[el.id].append($('<td name="'+name+'" value="' + val + '" >' + val + '</td>'));
            }
            $this._lines[el.id].click(function(el){console.log(el);
                var filters = {};
                $(el.currentTarget).find('td').each(function(index, el){
                    var el = $(el);
                    if(el.attr('name') && el.attr('value'))
                        filters[el.attr('name')] = el.attr('value');
                });
                console.log(filters);
                $this.options.data_container.setFilters(filters);
            });
            $this._lines[el.id].append($('<td>' + el.id + '</td>'));
            tbody.append($this._lines[el.id]);
        });
        table.append(thead);
        table.append(tbody);
        $this._table = table;
        this.element.append(table);
    },
    _refreshVisible: function () {
        var $this = this;
        $this._table.find('tr').hide('fast');
        $this.options.data_container.getFiltered().forEach(function (el) {
            $this._lines[el.id].show('slow');
        });
    }
})
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

