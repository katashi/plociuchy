//
// viewport layout
// 
function Viewport() {
	
	var viewport;
	var items;
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		items = new Array();
		if (_center) {
			center = new Center();
			center.init();
			this.add_ui(center.ui.display); 
		}
		if (_east) { 
			east = new East();
			east.init();
			this.add_ui(east.ui.display); 
		}
		if (_north) { 
			north = new North();
			north.init();
			this.add_ui(north.ui.display); 
		}
		if (_south) { 
			south = new South();
			south.init();
			this.add_ui(south.ui.display); 
		}
		if (_west) { 
			west = new West();
			west.init();
			this.add_ui(west.ui.display);
		}
	}
	
	//
	// ui section
	//
	
	//init
	this.init_ui = function() {
		
	}
	
	// construct
	this.construct_ui = function() {
		viewport = new Ext.Viewport({
			items: items,
			layout: 'border',
			monitorResize: true
		});
	}	
	
	// add [ for adding elements ]
	this.add_ui = function(region) {
		items.push(region);
	}
	
	// remove [ for removing elements ]
	this.remove_ui = function(region) {
	}
	
}