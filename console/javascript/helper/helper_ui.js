//
// ui helper
//
function Helper_Ui() {
	
	this.generate_uid = function() {
		return Math.floor(Math.random()*5000);
	}
	
	this.add_ui = function(where, what) {
		where.add(what);
	}
	
	this.remove_ui = function(where, what) {
		
	}
	
	this.add_window = function(id, title, url) {
		var config = new Object({
			autoLoad: {
				url: url,
				scripts:true
			},
			id: id,
			title: title,
			width: 500
		});
		helper_window = new Window();
		helper_window.init(config);
		helper_window.display.show();
	}
	
	this.remove_window = function() {
		
	}
	
	this.url_call = function(url, type, store, tree) {
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
    
    this.url_download = function(url) {
        document.location.href = url;
    }
    
    this.url_window = function(url) {
        window.open(url);
    }
	
	this.pretty_show = function(file_name, title) {
		$.prettyPhoto.open('../media/image/original/'+file_name, title);
	}
    
    this.pretty_show_video = function(path, title) {
		$.prettyPhoto.open(path);
	}
	
}