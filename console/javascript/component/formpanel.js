//
// form_panel
// 
function FormPanel() {
	
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
			border: false,
			buttonAlign: 'left',
			defaults: { 
				labelWidth: 350,
				width: 300
			},
			frame: true
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create
	this.display_create = function() {
		this.display = new Ext.FormPanel(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
	// load content
	this.formpanel_load = function() {
		this.display.form.load({
			url: this.config['formpanel_load_url'],
			waitMsg: 'Oczekiwanie...',
			success: function() {
			},
			failure: function() {
				alert('fail');
			}
		});
	}
	
}