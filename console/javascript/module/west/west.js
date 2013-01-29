//
// west
//
function West() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			bodyStyle: 'padding: 0px',
			border: true,
			id: 'west',
			region: 'west',
			split: true,
			title: 'Architektura',
			width: 250
		});
		this.ui = new Panel();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
		if (window._west_structure) { 
			west_structure = new West_Structure();
			west_structure.init();
			new Helper_Ui().add_ui(this.ui.display, west_structure.ui.display);
		}
		if (window._west_warehouse) { 
			west_warehouse = new West_Warehouse();
			west_warehouse.init();
			new Helper_Ui().add_ui(this.ui.display, west_warehouse.ui.display);
		}
		if (window._west_media) { 
			west_media = new West_Media();
			west_media.init();
			new Helper_Ui().add_ui(this.ui.display, west_media.ui.display);
		}
	}
	
}