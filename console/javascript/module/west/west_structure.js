//
// west structure
// 
function West_Structure() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			baseCls: 'ks',
			bodyStyle: 'padding: 0px',
			collapsed: true,
			collapsible: true,
			id: 'west_structure',
			title: 'Struktura',
			titleCollapse: true
		});
		this.ui = new Panel();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
		if (window._west_structure_website) { 
			west_structure_website = new West_Structure_Website();
			west_structure_website.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_structure_website.ui.display);
		}
		
	}
	
}