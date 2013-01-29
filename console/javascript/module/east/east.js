//
// east 
//
function East() {
	
	//
	// controller section
	//

	// init
	this.init = function() {
		// here we construct ui
		var config = new Object({
			collapsible: true,
			id: 'east',
			layout: 'table',
			region: 'east',
			split: true,
			title: 'Wydarzenia',
			width: 250
		});
		this.ui = new Panel();
		this.ui.init(config);
	}
	
}
