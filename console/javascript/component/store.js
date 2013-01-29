//
// store
// 
function Store() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.proxy_create();
		this.reader_create();
		this.display_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({ });
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// proxy create
	this.proxy_create = function() {
		this.proxy = new Ext.data.HttpProxy({
			method: 'POST',
			url: this.config.url
		});
	}
	
	// reader create / selector
	this.reader_create = function() {
		if (this.config.format == 'json') { 
			this.reader_json_create();
		}
		if (this.config.format == 'xml') { 
			this.reader_xml_create();
		}
	}
	
	// reader json create
	this.reader_json_create = function() {
		this.reader = new Ext.data.JsonReader({	
			root: 'data',
			totalProperty: 'total'
		}, this.config.fields);
	}
	
	// reader xml create
	this.reader_xml_create = function() {
		this.reader = new Ext.data.XmlReader({	
			record: 'item'
		}, this.config.fields);
	}

	// display create	
	this.display_create = function() {
		this.display = new Ext.data.Store({
            autoLoad: this.config.autoLoad,
			method: 'POST',
			proxy: this.proxy,
			reader: this.reader
		});
	}
	
}