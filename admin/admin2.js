//Plugin Labyrinth Admin
(function($,window,document,undefined){
	$.fn.labygraph = function(options){
		//default settings
		options = $.extend({
			background: "#222",
			fps: 60,
			addNodeForm: null,
			onNodeClick: function(){},
			onGraphClick: function(){},
			onPathClick: function(){}
		},options);
		
		//set window.graphs
		window.labygraphs = [];
		
		var createNode = function(options){
			var node = graph.display.arc({
				x: options.evt.x,
				y: options.evt.y
			});
		}
		
		//make it available for cascading
		return this.each(function(){
			
			//create a graph for each object in the list
			var graph = oCanvas.create({
				canvas: "#"+this.id,
				background: options.background,
				fps: options.fps
			});
			//an array containing the reference to all the nodes
			graph.nodes=[];
			//set the default action
			graph.nodeAction = "select";
			
			//When a click happens in graph, it means creating a new node 
			graph.bind("click", function(e){
				e.preventDefault();
				//display the container for image upload
				$("#addNode input[name=posX]").val(e.x);
				$("#addNode input[name=posX]").val(e.y);
				$("#addNode input[name=file]").click().change(function(){
					$("#addNode").ajaxSubmit({
						dataType:"json",
						success: function(response){
							console.log(response);
						}
					});
				});
			});			
			
			graph.bind("click", function(e){
				//draw a circle
				e.preventDefault();
				
				var node = graph.display.arc({
					x:e.x, y:e.y,
					radius: 5,
					start: 0, end: 360,
					fill: "#fff"
				});
				node.bind("mouseenter", function(evt){
					this.radius=7; this.redraw();
				}).bind("mouseleave", function(evt){
					this.radius=5; this.redraw();
				}).bind("click", function(evt){
					
					//alternative for stopPropagation	
					graph.mouse.cancel();
					evt.preventDefault();
					
					//possibilities - editing the node or deleting the node or path operations
					var node_num = 0;
					switch(graph.nodeAction){
						case "edit":
							this.fill="#abcdef";
							this.redraw();
							var that = this;
							options.onNodeEdit.apply(this,[function(){
								//end of edit
								that.fill = "#fff";
								that.redraw();
							}]);
							break;
						case "delete":
							options.onNodeDelete.apply(this,[]);
							break;
						case "path":
							options.onNodePath[node_num++].apply(this,[]);
							break;
					}
					
				});
				graph.nodes.push(node);	
				graph.addChild(node);
				return false;
			});
			window.graph = graph;
		});
	}
})(jQuery, this, this.document);


(function($,window,docment,undefined){
	//extend jQuery
	$("#addNode, #removeNode, #addPath, #removePath").submit(function(e){
		e.preventDefault();
		$(this).ajaxSubmit({
			dataType:"json",
			success: function(response){
				console.log(response);
			}
		});
		return false;
	});

	$("#graph").labygraph({
		
	});
	
})(jQuery,this,this.document);