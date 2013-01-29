//
// editor_grid_panel
// 
function EditorGridPanel() {
	
	// init
	this.init = function(config) {
		this.config_create(config);
		this.display_create();
		this.event_create();
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({
			autoHeight: true,
			clicksToEdit: 1,
			frame: true,
			sm: new Ext.grid.RowSelectionModel({ singleSelect: true }),
			viewConfig: {
				forceFit: true
			}
		});
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// display create	
	this.display_create = function() {
		this.display = new Ext.grid.EditorGridPanel(this.config);
		// assign main object to display
		this.display.parent = this;
	}
	
	// events create 
	this.event_create = function() {
		this.display.on('afteredit', function(obj) {
			//this.parent.update(obj);
		});
	}
	
	// update
	this.update = function(obj) {
		var params = new Object();
		params.id = obj.record.data['id'];
		// find dirty
		for (var a in obj.record.data) {
			if (obj.record.isModified(a)) {
				params[a] = obj.record.data[a];
			}
		}
		// options
		var options = new Object();
		options.url = this.config.url;
		options.params = params;
		// connection
		var conn = new Ext.data.Connection();
		conn.method = 'POST';
		conn.disableCaching = false;
		conn.on('requestcomplete', function(conn, response){
			//response = Ext.decode(response.responseText);
		}, this);
		conn.request(options);
	}
	
}