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
		window.labygraph = {};
		window.labygraph.items = [];
		
		var handleClick = function() {
			switch(a){
				case "editNode":
									
				case "deleteNode":
					break;
			}
		};
		
		var createNode = function(options){
			var graph = options.graph;
			var node = graph.display.arc({
				x: options.posX,
				y: options.posY,
				radius: 5,
				start: 0, end: 360,
				fill: "#fff"
			});
			//next step is to bind the event listeners to the node
			node.bind("mouseenter",function(){
				this.radius=7; this.redraw();
			}).bind("mouseleave", function(){
				this.radius=5; this.redraw();
			}).bind("click",function(){
				graph.mouse.cancel();
				handleClick.apply(this,[]);
			});
			graph.nodes.push(node);
			graph.addChild(node);
		}
		//make it available Globally, to use it as an API
		window.labygraph.createNode = createNode;
		
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
				var self= this;
				//display the container for image upload
				$("#addNode input[name=posX]").val(e.x);
				$("#addNode input[name=posY]").val(e.y);
				$("#addNode input[name=file]").click().change(function(){
					$("#addNode").ajaxSubmit({
						dataType:"json",
						success: function(data){
							if(data.status!=600){
								console.log(data.message);
								return;
							}
							createNode.apply(self,[{
								graph: graph,
								posX: data.posX,
								posY: data.posY
							}]);
						}
					});
				});
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