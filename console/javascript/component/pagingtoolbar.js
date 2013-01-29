//
// pagingtoolbar
//
function PagingToolBar() {
	
	// init
	this.init = function(config) { 
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({
			displayInfo: true,
			displayMsg: 'Elementy {0} - {1} z {2}',
			emptyMsg: 'Brak elementów',
			pageSize: _paging_limit
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create
	this.display_create = function() {
		this.display = new Ext.PagingToolbar(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
	// button add
	this.button_add = function(config) {
		
		
	} 	
	
	// button remove
	this.button_remove = function(config) {
		
	}
	
}