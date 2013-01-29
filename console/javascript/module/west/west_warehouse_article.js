//
// west warehouse article
// 
function West_Warehouse_Article() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			collapsible: true,
			title: 'Artykuły',
			titleCollapse: true,
			tools: [
			    { id:'save', qtip: 'Dodaj Artykuł', handler: this.directory_add },
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
            controller: '_warehouse:',
			dataUrl: base_url+'/_system:tree/tree_create/warehouse_article',
			ddGroup: 'warehouse_article',
            ddGroupExtended: 'structure_website',
			id: 'warehouse_article'
		});
		this.ui_tree = new Tree();
		this.ui_tree.init(config);
		new Helper_Ui().add_ui(this.ui.display, this.ui_tree.display);
	}
	
	// directory_add
	this.directory_add = function() {
		new Helper_Ui().add_window('warehouse_article_directory_add', 'Dodaj katalog', base_url +'/_system:tree/add/warehouse_article,0');
	}
	
	// refresh content
	this.refresh = function() {
		west_warehouse_article.ui_tree.root.reload();
		west_warehouse_article.ui_tree.path_expand();
	}
	
}