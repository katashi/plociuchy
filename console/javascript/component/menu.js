//
// menu
// 
function Menu() {

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
	
	// empty ui component
	this.display_create = function() {
		// creating menu
		this.display = new Ext.menu.Menu(this.config);
		// load content from url and handling
		this.display.load({
			url: this.config.url,
			handler: function(item) {
				if (item.type == 'href') {
					location.href = item.url;
				}
				if (item.type == 'tab') {
					center.ui.tab_add(item.id, item.text, item.url);
				} 
			},
			enableToggle: true
		});
		// assign main object to display
		this.display.parent = this;
	}
	
	// prototype for menu load
	Ext.menu.Menu.prototype.load = function(options){
		var loader = new Ext.menu.Item({text: '_loading'});
		var conn = new Ext.data.Connection();
		conn.method = 'GET';
		conn.disableCaching = false;
		this.addItem(loader);
		conn.on('requestcomplete', function(conn,response){
			this.remove(loader);
			response = Ext.decode(response.responseText);
			Ext.each(response.menu, function(item){ 
				item.handler = options.handler;
				this.add(item); 
			}, this);
		}, this);
		conn.on('requestexception', function(){
			this.remove(loader);
			this.add({text: '_error1'});
		}, this);
		conn.request(options);
	}
	
}