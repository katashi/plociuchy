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
		if (window._west_media_file) { 
			west_media_file = new West_Media_File();
			west_media_file.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_media_file.ui.display);
		}
		if (window._west_media_image) { 
			west_media_image = new West_Media_Image();
			west_media_image.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_media_image.ui.display);
		}
		if (window._west_media_video) { 
			west_media_video = new West_Media_Video();
			west_media_video.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_media_video.ui.display);
		}
		
	}
	
}