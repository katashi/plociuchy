//
// toolbar
// 
function Toolbar() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({
			height: 25
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create	
	this.display_create = function() {
		this.display = new Ext.Toolbar(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
}