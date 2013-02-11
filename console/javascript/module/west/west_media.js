//
// west media
// 
function West_Media() {
	
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
			id: 'west_media',
			title: 'Media',
			titleCollapse: true
		});
		this.ui = new Panel();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
		if (window._west_media_image) { 
			west_media_image = new West_Media_Image();
			west_media_image.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_media_image.ui.display);
		}
		
	}
	
}