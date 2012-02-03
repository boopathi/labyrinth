//Plugin Labyrinth Admin
(function($,window,document,undefined){
	$.fn.labygraph = function(options){
		//default settings
		options = $.extend({
			background: "#222",
			fps: 60,
			onNodeEdit: function(){},
			onNodeDelete: function(){},
			onNodeSelect: [function(){}, function(){}]
		},options);
		//make it available for cascading
		return this.each(function(){
			var graph = oCanvas.create({
				canvas: "#"+this.id,
				background: options.background,
				fps: options.fps
			});
			graph.nodes=[];
			graph.nodeAction = "edit";
			graph.bind("click", function(e){
				//draw a circle
				e.preventDefault();
				
				//put graph data on server, and bind the following lines of code within the success hook
				//ajax request
				
				$.ajax({
					type : "POST",
					url : "index.php?_a=1",
					data : {
						action:"addNode"
						},
					dataType : "json" ,
					success : function(data){
						if(data.status != 600 ){
							console.log(data.message);
							return false;
						}
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
					},
					error : function(){
						
					}
				});
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
	$("#addNode, #removeNode, #addPath, #removePath").ajaxForm({
		dataType: "json",
		success: function(response){
			console.log(response);
		}
	});

	$("#graph").labygraph({
		onNodeEdit: function(callback){
			console.log(this);
			$("#nodeEditor").slideDown(50);
			$("#nodeEditor .closebutton").click(function(e){
				e.preventDefault();
				$(this).parent(".floater").slideUp(100);
				callback.call();
			});
		},
		onNodeDelete: function(callback){
			callback.call();
		},
		onNodePath: function(callback){
			callback.call();
		}
		
	});
	
})(jQuery,this,this.document);