//
// north plociuchy
// 
function North_Plociuchy() {
	
	//
	// controller section
	//
	
	// init
	this.init = function() {
		// predefine menu to attach to button
		var config = new Object({
			id: 'north_plociuchy_menu',
			url: base_url+'/_system:menu/get/plociuchy'
		});
		this.ui_menu = new Menu();
		this.ui_menu.init(config);
		// defining temporary menu value
		var menu = this.ui_menu.display;
		
		// here we construct ui
		var config = new Object({
			iconCls: 'brick',
			text: 'Plociuchy',
			tooltip: {title: 'Plociuchy', text: '', autoHide:true},
			menu: menu
		});
		this.ui = new Button();
		this.ui.init(config);
	}
	
}