//Plugin Labyrinth Admin
(function($,window,document,undefined){
	$.fn.labygraph = function(options){
		//default settings
		options = $.extend({
			background: "#628d00",
			fps: 60,
			addNodeForm: null,
			onNodeClick: function(){},
			onGraphClick: function(){},
			onPathClick: function(){}
		},options);
		
		//set globally accesible objects
		window.labygraph = {};
		window.labygraph.items = [];
		
		var handlerObject = {
			"viewnode": function() {
				var self = this;
				$.ajax({
					url: "./index.php?_a=1",
					type:"POST",
					dataType: "json",
					data: {
						action: "showNode",
						level: this.qno
					},
					success: function(data){
						if(data.status != 600){
							console.log(data.message);
							return;
						}
						//append the loaded html and resize the image
						$("#viewNode").html(data.html).css({
							top: $("#graph").offset().top + self.posY - 3,
							left: $("#graph").offset().left + self.posX + 10
						}).find("img").css({
							width: 60, height: 60
						});
					}
				});
			},
			"editNode": function(){
				$("#addNode input[name=posX]").val(this.posX);
				$("#addNode input[name=posY]").val(this.posY);
				$("#addNode input[name=file]").click().change(function(){
					$("#addNode").ajaxSubmit({
						dataType:"json",
						success: function(data){
							if(data.status!=600){
								console.log(data.message);
								return;
							}
							console.log("edited successfully");
						}
					});
				});
			},
			"deleteNode": function(){
				
			},
			"createPath": function(options){
				//this function is the ajax request
			}
		}
		
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
				stroke: "2px #fff"
			});
			graph.path.items.push(path);
			graph.addChild(path);
			$("#showTextBox").hide();
			//clear once the path is drawn;
			if(typeof options.callback === "function")
				options.callback.apply(this,[]);
		};
		
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
				//view the node
				handlerObject["viewnode"].apply(this,[]);
			}).bind("mouseleave", function(){
				this.radius=5; 
				this.redraw();
				$("#viewNode").html("");
			}).bind("click",function(){
				graph.mouse.cancel();
				//decide which one to call - node or path
				if(graph.isCtrl){
					//when CRTL is down, record the first and second nodes
					if(typeof graph.path.firstNode === "undefined"){
						//the selection is the first node
						graph.path.firstNode = this;
						this.fill = "#f00";
						this.redraw();
						$("#addPath input[name=from]").val(this.qno);
					} else if (typeof graph.path.secondNode === "undefined"){
						//if second is not defined, then set the second value
						graph.path.secondNode = this;
						this.fill = "#f00";
						this.redraw();
						$("#addPath input[name=to]").val(this.qno);
						//after the second node is set, display the dialog box to enter the answer
						var self = this;
						$("#showTextBox").css({
							top: $("#graph").offset().top + this.posY - 3,
							left: $("#graph").offset().left + this.posX + 10
						}).show().find("input").val("").focus();
					} else {
						console.log("Some error happened. Deleting the values");
						delete graph.path.firstNode;
						delete graph.path.secondNode;
					}
				} else {
					//he is editing the current node
					handlerObject["editNode"].apply(this,[]);
				}
			});
			graph.nodes.push(node);
			graph.addChild(node);
		}
		//make it available Globally, to use it as an API
		window.labygraph.createNode = createNode;
		window.labygraph.createPath = createPath;
		
		var getNodePointer = function(qno, graph) {
			var r = 0, c;
			for( c = 0; c < graph.nodes.length; c++)
				if(graph.nodes[c].qno == qno)
					return graph.nodes[c];
		}

		//function to initialize the graph
		
		var initGraph = function(graph) {
			var data = graph_data;
			$(data.nodedata).each(function() {
				if(this.level !== 0) {
					createNode.apply(this, [{
						graph : graph,
						posX : this.posX,
						posY : this.posY,
						nodeId : this.level
					}]);
				}
			});
			$(data.pathdata).each(function() {
				var from = getNodePointer(this.from,graph);
				var to = getNodePointer(this.to,graph);
				createPath.apply(this, [{
					graph : graph,
					start : {
						x : from.posX,
						y : from.posY
					},
					end : {
						x : to.posX,
						y : to.posY
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
			
			graph.isCtrl=false;

			$(document).bind({
				"keydown":function(e){
					graph.isCtrl = e.which === 17;
				},
				"keyup": function(e){
					graph.isCtrl = !(e.which === 17);
				}
				
			});
			
			//bind to the textbox
			$("#showTextBox").find("input").bind({
				"keyup": function(e){
					if(e.which === 27) {
						graph.path.firstNode && 
						graph.path.secondNode && 
						(graph.path.firstNode.fill = graph.path.secondNode.fill = "#fff");
						graph.path.firstNode && graph.path.firstNode.redraw();
						graph.path.secondNode && graph.path.secondNode.redraw();
						delete graph.path.firstNode;
						delete graph.path.secondNode;
						$(this).parent("#showTextBox").hide();
					}
					if(e.which === 13){
						//create the path
						e.preventDefault();
						$("#addPath input[name=key]").val($(this).val());
						//create the path with ajax request
						$("#addPath").ajaxSubmit({
							dataType:"json",
							success: function(data){
								if(data.status!==600){
									console.log(data.message);
									return;
								}
								createPath.apply(self,[{
									graph: graph,
									start: {
										x:graph.path.firstNode.posX,
										y:graph.path.firstNode.posY
									},
									end: {
										x:graph.path.secondNode.posX,
										y:graph.path.secondNode.posY
									}, 
									callback: function(){
										graph.path.firstNode.fill = graph.path.secondNode.fill = "#fff";
										graph.path.firstNode.redraw();
										graph.path.secondNode.redraw();
										delete graph.path.firstNode;
										delete graph.path.secondNode;
									}
								}]);
							},
							error: function(xhr,err){
								console.log(err);
								$(e.target).hide();
							}
						});
					}
				}
			});
			
			//bind to click for node create
			$("#addNode").ajaxForm({
				dataType:"json",
				success: function(data){
					if(data.status!=600){
						console.log(data.message);
						return;
					}
					createNode.apply(this,[{
						graph: graph,
						posX: data.posX,
						posY: data.posY,
						nodeId: data.nodeId
					}]);
					$("#nodeheaderBox").hide();
				}
			});
			
			//When a click happens in graph, it means creating a new node 
			graph.bind("click", function(e){
				e.preventDefault();
				var self= this;
				//display the container for image upload
				$("#addNode input[name=posX]").val(e.x);
				$("#addNode input[name=posY]").val(e.y);
				$("#addNode input[name=file]").click().change(function(){
					$("#addNode input[name=header]").focus();
					$("#nodeheaderBox").css({
						left: $("#graph").offset().left+($("#graph").width()/2)-($(this).width()/2), 
						top:$("#graph").offset().top+($("#graph").height()/2)-100
					}).show();
				});
			});
			
			
			//init the graph
			initGraph.apply(this,[graph]);
			
			window.labygraph.items.push(graph);
			window.graph = graph;
		});
	}
})(jQuery, this, this.document);

(function($,window,document,undefined){
	//extend jQuery
	
	$("#graph").labygraph();
	
})(jQuery,this,this.document);