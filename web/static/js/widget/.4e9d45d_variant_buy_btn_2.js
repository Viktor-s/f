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

