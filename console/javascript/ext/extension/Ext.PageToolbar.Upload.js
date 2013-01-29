Ext.override(Ext.PagingToolbar, {
    doLoad : function(start){
        var o = {}, pn = this.getParams();
        o[pn.start] = start;
        o[pn.limit] = this.pageSize;
        if (this.store.lastOptions) {
            Ext.applyIf(o, this.store.lastOptions.params);
        }
        if(this.fireEvent('beforechange', this, o) !== false){
            this.store.reload({params:o});
        }
    }
});