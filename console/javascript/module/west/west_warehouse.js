//
// west warehouse
// 
function West_Warehouse() {
	
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
			id: 'west_warehouse',
			title: 'Magazyn',
			titleCollapse: true
		});
		this.ui = new Panel();
		this.ui.init(config);
		//
		//
		// here we check predefined components for launch ( fixed code - no config )
		//
		//
		if (window._west_warehouse_article) { 
			west_warehouse_article = new West_Warehouse_Article();
			west_warehouse_article.init(); 
			new Helper_Ui().add_ui(this.ui.display, west_warehouse_article.ui.display);
		}

	}
	
}