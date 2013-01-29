//
// window
// 
function Window() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({ 
			bodyStyle: 'padding: 5px',
            id: this.config.id,
            title: this.config.title
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create	
	this.display_create = function() {

		this.display = new Ext.Window(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
}