//
// combobox
//
function ComboBox() {
	
	// init
	this.init = function(config) { 
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({ });
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	this.display_create = function() {
		this.display = new Ext.form.ComboBox(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
}