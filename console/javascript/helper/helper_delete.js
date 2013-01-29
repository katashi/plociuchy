//
// delete helper
//
function Helper_Delete() {
	
	this.delete_confirm = function(url, type, store, tree) {
		Ext.MessageBox.confirm('Usunięcie elementu', 'Jesteś pewien?', function(result) {	
			if (result == 'yes') {
				var connection = new Ext.data.Connection();
				connection.request({
					url: url,
					method: 'POST',
					params: { disableCaching: true },
					success: function(responseObject) {
						//alert(type+','+store+','+tree);
						if (type == 'grid') {
							// store reload
							store.display.reload();
							// tree reload
							if (tree) {
								tree.root.reload();
								tree.path_expand();
							}
						}
					}
					
				});
				
			}
			
		});
		
	}
	
}