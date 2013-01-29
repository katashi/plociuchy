//
// west structure website
// 
function West_Structure_Website() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			collapsible: true,
			title: 'Strona Internetowa',
			titleCollapse: true,
			tools: [
			    { id:'save', qtip: 'Dodaj Katalog', handler: this.directory_add },
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
            controller: '_structure:',
			dataUrl: base_url+'/_system:tree/tree_create/structure_website',
            ddGroup: 'structure_website',
			id: 'structure_website'
		});
		this.ui_tree = new Tree();
		this.ui_tree.init(config);
		new Helper_Ui().add_ui(this.ui.display, this.ui_tree.display);
	}
	
	// directory_add
	this.directory_add = function() {
		new Helper_Ui().add_window('structure_website_directory_add', 'Dodaj Katalog', base_url +'/_system:tree/add/structure_website,1');
	}
	
	// refresh content
	this.refresh = function() {
		west_structure_website.ui_tree.root.reload();
		west_structure_website.ui_tree.path_expand();
	}
	
}