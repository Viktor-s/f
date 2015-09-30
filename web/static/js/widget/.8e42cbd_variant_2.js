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
                            ._get_select_element(option_name);

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
        _get_select_element: function (name) {
            return $('<tr><td>' + name + '</td><td>\
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