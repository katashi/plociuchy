//
// panel
// 
function Panel() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		defaults = new Object({
			autoScroll: true,
			border: false,
			bodyStyle: 'padding: 3px',
			closable: true,
			collapsible: false,
			titleCollapse: false
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create
	this.display_create = function() {
		this.display = new Ext.Panel(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
}