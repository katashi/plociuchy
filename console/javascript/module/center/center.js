//
// center
//
function Center() {
	
	//
	// controller section
	//

	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			id: 'center',
			region: 'center',
			title: 'Zawartość'
		});
		this.ui = new TabPanel();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
	}

}