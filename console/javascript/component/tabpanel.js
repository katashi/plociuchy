//
// tabpanel
// 
function TabPanel() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({
			enableTabScroll: true,
			items: [{
				id: 'Katashi',
				title: 'Katashi',
				closable: true,
				autoLoad: {
					url: 'index.php/main/welcome',
					scripts: true
				}
			}]
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create
	this.display_create = function() {
		this.display = new Ext.TabPanel(this.config);
		this.display.setActiveTab('Katashi');
		// assign main object to display
		this.display.parent = this;
	}
	
	// tab add
	this.tab_add = function(id, title, url) {
		this.tab_refresh(id);
		// here we construct ui
		var config = new Object({
			autoLoad: {
				url: url,
				scripts:true
			},
			id: id,
			title: title
		});
		this.tab = new Panel();
		this.tab.init(config);
		new Helper_Ui().add_ui(this.display, this.tab.display);
		this.display.setActiveTab(id);
	}
	
	// tab refresh
	this.tab_refresh = function(id) {
		var tab_to_find = center.ui.display.findById(id);
		if (tab_to_find) { 
			var tab = tab_to_find.getUpdater();
			if (tab) { tab.refresh(); }
		}
	}
	
}