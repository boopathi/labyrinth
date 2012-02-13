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
		
		//set globally accesible objects
		window.labygraph = {};
		window.labygraph.items = [];
		
		//The following function is just drawPath.. and not createPath
		var createPath = function(options){
			//add offset
			var offset={};
			offset.x = (options.start.x < options.end.x) ? 3 : -3;
			offset.y = (options.start.y > options.end.y) ? 3 : -3;
			options.start.x+=offset.x;
			options.start.y+=offset.y;
			options.end.x+=offset.x;
			options.end.y+=offset.y;
			var graph = options.graph;
			var path = graph.display.line({
				start: options.start,
				end: options.end,
				stroke: "1px #aaa"
			});
			graph.path.items.push(path);
			graph.addChild(path);
			$("#showTextBox").hide();
			//clear once the path is drawn;
			if(typeof options.callback === "function")
				options.callback.apply(this,[]);
		};
		//to display statistics
		var handlerObject = function() {
			var self = this;
			$("#viewNode").html(stats_data.nodearray['level']).css({
				top: $("#graph").offset().top + self.posY - 3,
				left: $("#graph").offset().left + self.posX + 10
			});
		}
			
		//The following function is just drawNode.. and not createNode
		var createNode = function(options){
			//`this` is not important inside this function. Don't worry about it when using `apply`
			var graph = options.graph;
			var node = graph.display.arc({
				x: options.posX,
				y: options.posY,
				radius: 5,
				start: 0, end: 360,
				fill: "#fff"
			});
			//set the id and position for the node
			node.qno = options.nodeId;
			node.posX = options.posX;
			node.posY = options.posY;
			//next step is to bind the event listeners to the node
			node.bind("mouseenter",function(){
				this.radius=7; 
				this.redraw();
				//call to display statistics
				handlerObject.apply(this,[]);
			}).bind("mouseleave", function(){
				this.radius=5; 
				this.redraw();
				$("#viewNode").html("");
			});
			graph.nodes.push(node);
			graph.addChild(node);
		}
		//make it available Globally, to use it as an API
		window.labygraph.createNode = createNode;
		window.labygraph.createPath = createPath;
		
		var getNodePointer = function(qno) {
			var r = 0, c;
			while( typeof window.labygraph.items[r] !== "undefined") {
				for( c = 0; c < window.labygraph.items[r].nodes.length; c++)
					if(window.labygraph.items[r].nodes[c].qno == qno)
						return window.labygraph.items[r].nodes[c];
				r++;
			}
		}

		//function to initialize the graph
		var initGraph =  function (graph) {
			
					$(stats_data.nodearray).each(function(){
						if(this.level !== 0){
							createNode.apply(this,[{
								graph: graph,
								posX: this.posX,
								posY: this.posY,
								nodeId: this.level
							}]);
						}
					});
					$(stats_data.patharray).each(function(){
						var from = getNodePointer(this.from);
						var to = getNodePointer(this.to);
						createPath.apply(this,[{
							graph: graph,
							start: {
								x:from.posX,
								y:from.posY
							},
							end: {
								x:to.posX,
								y:to.posY
							}
						}]);
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
			//an array containing the path data
			graph.path={};
			graph.path.items=[];
			

			
			
			initGraph.apply(this,[graph]);
			
			window.labygraph.items.push(graph);
			window.graph = graph;
		});
	}
})(jQuery, this, this.document);

(function($,window,document,undefined){
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

	$("#graph").labygraph();
	
})(jQuery,this,this.document);