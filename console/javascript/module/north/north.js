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
        if (window._north_admin_hq) {
            north_admin_hq = new North_Admin_Hq();
            north_admin_hq.init();
            new Helper_Ui().add_ui(this.ui.display, north_admin_hq.ui.display);
        }
        if (window._north_admin_office) {
            north_admin_office = new North_Admin_Office();
            north_admin_office.init();
            new Helper_Ui().add_ui(this.ui.display, north_admin_office.ui.display);
        }
        if (window._north_admin_dms) {
            north_admin_dms = new North_Admin_Dms();
            north_admin_dms.init();
            new Helper_Ui().add_ui(this.ui.display, north_admin_dms.ui.display);
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