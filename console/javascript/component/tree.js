//
// tree
// 
function Tree() {
	
	this.path = null;
	
	// init
	this.init = function(config) {	
		this.config_create(config);
		this.context_create();
		this.loader_create();	
		this.display_create(); 
		this.root_create();
		this.event_create();
		this.display.setRootNode(this.root);
		this.root.expand(false, false);
	}
	
	// config create
	this.config_create = function(config) {
		this.config = config;
		var defaults = new Object({ });
		for (value in defaults) { if (this.config[value] == undefined) { this.config[value] = defaults[value]; } }
	}
	
	// loader create
	this.loader_create = function() { 
		this.loader = new Ext.tree.TreeLoader({
			dataUrl: this.config.dataUrl
		});
		this.loader.on("beforeload", function(tl, node) {
			if (node.attributes.level) {
				this.loader.baseParams.level = node.attributes.level;
			} else {
				this.loader.baseParams.level = '0';
			}
		}, this);
		this.loader.on("load", function(tl, node) { 
		}, this);
		this.loader.on("loadexception", function(tl, node) {
			alert('Error!');
		}, this);
	}

	// display create
	this.display_create = function() {
		this.display = new Ext.tree.TreePanel({
			animate: false,
			animCollapse: false,
			border: false,
			contextMenu: this.context,
			ddGroup: this.config.ddGroup,
			enableDD: true,
			id: this.config.id,
			loader: this.loader,
			rootVisible: false
		});
		// assign main object to display
		this.display.parent = this;
	}
	
	// context create
	this.context_create = function() {
		/*this.context = new Ext.menu.Menu({
			items: [{
				iconCls: 'folder_delete',
				id: 'folder_delete',
				text: 'Usuń Katalog'
			}],
			listeners: {
				itemclick: function(item) {
					var ajax_request = false;
					switch (item.id) {
						case 'folder_delete':
                            //ajax_request = true;
                            url = 'aaaa';
						break;
					}
					if (ajax_request) {
						Ext.Ajax.request({
							method: 'GET',
							timeout: 90000,
							url: url,
							success: function(response, request) {
								if (response.responseText.charAt(0) != 1){
									request.failure();
								} else {
									tree.enable();
								}
							},
							failure:function() {
								tree.suspendEvents();
								tree.resumeEvents();
								tree.enable();
							}
						});
					};
				}
			}
		});*/
	}
		
	// events create 
	this.event_create = function() {
		this.display.on('beforeexpandnode', function(node, deep, anim) {
			this.parent.path_set(node);
		});
		this.display.on('click', function(node, e) {
			this.parent.path_set(node);
			this.parent.node_click(node);
		});
		this.display.on('contextmenu', function(node, e) {
			context_genre = node.attributes['genre'];
            context_tree = node.attributes['tree'];
            context_id_element = node.attributes['id_element'];
            context_id_relations = node.attributes['id_relations'];
			node.select();
			var c = node.getOwnerTree().contextMenu;
			c.contextNode = node;
			c.showAt(e.getXY());
		});
		this.display.on('startdrag', function(tree, node, event) {
			this.tree_source = tree;
			this.oldPosition = node.parentNode.indexOf(node);
			this.oldNextSibling = node.nextSibling;
		});
		this.display.on('enddrag', function(tree, node, event) {
		});
		this.display.on('movenode', function(tree, node, oldParent, newParent, position) {
            if (this.tree_source) {		  
    			id = this.parent.config.id;
    			// verify type
    			if (oldParent == newParent){
    				if (node.attributes['genre'] == 'tree') {
    					// for nodes
    					var url = base_url+'/_system:tree/tree_create/'+id+',reorder';
    					var params = {'node': node.attributes['id'], 'delta': (position-this.oldPosition) };
    				} else {
    					// for physical files
    					var url = base_url+'/_system:tree/tree_create/'+id+',reorder_element';
    					var params = {'node': node.attributes['id_element'], 'delta': (position-this.oldPosition) };					
    				}
    			} else {
    				if (node.attributes['genre'] == 'tree') {
    					// for nodes
    					var url = base_url+'/_system:tree/tree_create/'+id+',reparent';
    					var params = {'node': node.attributes['id'], 'node_structure': node.attributes['id_element'], 'parent': newParent.id, 'position': position };
    				} else {
    					// for physical files
    					var url = base_url+'/_system:tree/tree_create/'+id+',reparent_element';
    					var params = {'node': node.attributes['id_element'], 'node_structure': node.attributes['id_relations'], 'parent': newParent.id, 'position': position };
    				}
    				
    			}
    			// params correction
    			if (params['parent'] < 0) { params['parent'] = 0; }
    			// ajax request
    			Ext.Ajax.request({
    				method: 'POST',
    				params: params,
    				timeout: 1000,
    				url: url,
    				success: function(response, request) { if (response.responseText.charAt(0) != 1){ request.failure(); } else { } },
    				failure:function() { oldParent.appendChild(node); if (oldNextSibling){ oldParent.insertBefore(node, oldNextSibling); } }
    			});
            }
		});
		this.display.on('nodedrop', function(obj) {
            /*tree - The TreePanel
			target - The node being targeted for the drop
			data - The drag data from the drag source
			point - The point of the drop - append, above or below\
		    source - The drag source
			rawEvent - Raw mouse event
			dropNode - Dropped node(s).*/
            //
            var tree_source = obj['source'].tree; 
            var tree_target = this.parent.display;
            var target = obj['target']['attributes'];
            var dropNode = obj['dropNode']['attributes'];  
            // first we need to know does target is node, dropnode is an element and tree are different
            if (target.genre == 'tree' && dropNode.genre == 'element' && (target.tree != dropNode.tree)) {
                var url = base_url+'/_system:tree/tree_create/'+target.tree+',assign';
                var params = {
                    'id_tree': target.id, 
                    'id_element': dropNode.id_element
                }
                // if we deal with a website_structure we need to add another element
                if (target.tree == 'structure_website') {
                    params['type'] = dropNode.tree;
                }
                // lets disable tree for a while
                tree_source.disable();
                tree_target.disable();
                Ext.Ajax.request({
                    method: 'POST',
                    params: params,
                    url: url,
                    success: function(response, request) {
                        tree_source.root.reload();
                        tree_source.root.expand(false, false);
                        tree_source.enable();
                        tree_target.root.reload();
                        tree_target.root.expand(false, false);
                        tree_target.enable();
					},
					failure:function() {
                        tree_source.suspendEvents();
                        tree_source.resumeEvents();	
                        tree_source.enable();
                        //tree_source.root.reload();
                        //tree_source.root.expand(false, false);
                        tree_target.suspendEvents();
                        tree_target.resumeEvents();	
                        tree_target.enable();
                        //tree_target.root.reload();
                        //tree_target.root.expand(false, false);
					}
                });  
            }
			//for(var a in obj['dropNode']['attributes']) { alert(a+','+obj['dropNode']['attributes'][a]); }
			//for(var a in obj['target']['attributes']) { alert(a+','+obj['target']['attributes'][a]); }
		}); 
        this.display.on('afterrender', function(obj) {
           if (this.parent.config.ddGroupExtended) { this.parent.dragarea_create(); }
        });
	}

    // drag area
    this.dragarea_create = function() {
        // yes, its a little bit complicated,
        // whenever you add a extendedgroup you can only drag TO assigned group
        // opposite way is rather impossible
        var dragZone = this.display.dragZone;
        var ddGroupExtended = new Array();
        ddGroupExtended =  this.config.ddGroupExtended.split(',');
        for (var a in ddGroupExtended) {
            dragZone.addToGroup(ddGroupExtended[a]);
        }
    }
	
	// root create
	this.root_create = function() {
		this.root = new Ext.tree.AsyncTreeNode({
			element: true,
			expanded: false,
			id: '-1',
			text: 'start'
		});
	}
	
	// path set
	this.path_set = function(node) { this.path = node.getPath(); }
	
	// path get
	this.path_get = function(node) { return this.path; }
	
	// path expand
	this.path_expand = function() { this.display.expandPath(this.path_get()); }
	
	// node click
	this.node_click = function(node) {
		if (node.attributes['genre'] == 'element') {
            switch (node.attributes['type']) {
                case 'warehouse_article': controller = '_warehouse:'; break;
            }
            //
            if (node.attributes['type'] == 'media_image') {
                new Helper_Ui().pretty_show(node.attributes['file_name'],node.attributes['orig_name']);
            } else {
                // all major elements which have to be opened in tab
                tab_selected_id = node.attributes['id_element']
                tab_selected_tree = node.attributes['type'];
                tab_id = node.attributes['type'] + '_edit_' + node.attributes['id_element'];
                tab_title = node.attributes['text'];
                tab_url = base_url +'/'+ controller + node.attributes['type'] +'/display_edit/'+ node.attributes['id_element'];
                center.ui.tab_add(tab_id, tab_title, tab_url);
            }
		}
		if (node.attributes['genre'] == 'tree') {
			tab_selected_id = node.attributes['id_element'];
			tab_selected_tree = this.config['id'] + '_tree';
			tab_id = this.config['id'] + '_all_' + node.attributes['id_element'];
			tab_title = node.attributes['text'];
			tab_url = base_url +'/'+ this.config['controller'] + this.config['id'] +'/display/'+ node.attributes['id_element'];
			center.ui.tab_add(tab_id, tab_title, tab_url);
		}
	}
}