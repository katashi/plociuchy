//
// west media image
// 
function West_Media_Image() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			collapsible: true,
			title: 'Obrazy',
			titleCollapse: true,
			tools: [
			    { id:'save', qtip: 'Dodaj katalog', handler: this.directory_add },
			    { id:'refresh', qtip: 'Odśwież', handler: this.refresh }
			]
		});
		this.ui = new Panel();
		this.ui.init(config);
		//
		//
		// here we run fixed elements for section
		//
		//
		var config = new Object({
            controller: '_media:',
			dataUrl: base_url+'/_system:tree/tree_create/media_image',
			ddGroup: 'media',
			id: 'media_image'
		});
		this.ui_tree = new Tree();
		this.ui_tree.init(config);
		new Helper_Ui().add_ui(this.ui.display, this.ui_tree.display);
	}
	
	// directory_add
	this.directory_add = function() {
		new Helper_Ui().add_window('media_image_directory_add', 'Dodaj katalog', base_url +'/_system:tree/add/media_image,0');
	}
	
	// refresh content
	this.refresh = function() {
		west_media_image.ui_tree.root.reload();
		west_media_image.ui_tree.path_expand();
	}
	
}