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