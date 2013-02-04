//
// north
//
function North() {
	
	//
	// controller section
	//

	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			id: 'north',
			region: 'north',
			title: 'Menu'
		});
		this.ui = new Toolbar();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
		/*if (window._north_newsletter) {
			north_newsletter = new North_Newsletter();
			north_newsletter.init();
			new Helper_Ui().add_ui(this.ui.display, north_newsletter.ui.display);
		}*/

		//
		// custom components
		//
        if (window._north_plociuchy) {
            north_plociuchy = new North_Plociuchy();
            north_plociuchy.init();
            new Helper_Ui().add_ui(this.ui.display, north_plociuchy.ui.display);
        }

        //
		// system
		//
		if (window._north_system) { 
			north_system = new North_System();
			north_system.init();
			new Helper_Ui().add_ui(this.ui.display, north_system.ui.display);
		}

	}
	
}