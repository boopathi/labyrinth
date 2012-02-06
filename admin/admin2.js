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
				})
			},
			"editnode": function(){
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
				
			}
		}
		
		var createPath = function(options){
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
			options.callback.apply(this,[]);
		};
		
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
						$("#showTextBox").val("").bind({
							"keypress": function(e){
								if(e.which == 13){
									//create the path
									e.preventDefault();
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
								}
							}
						}).css({
							top: $("#graph").offset().top + this.posY - 3,
							left: $("#graph").offset().left + this.posX + 10
						}).show().find("input").focus();
					} else {
						console.log("Some error happened. Deleting the values");
						delete graph.path.firstNode;
						delete graph.path.secondNode;
					}
				}
			});
			graph.nodes.push(node);
			graph.addChild(node);
		}
		//make it available Globally, to use it as an API
		window.labygraph.createNode = createNode;
		window.labygraph.createPath = createPath;
		
		//function to initialize the graph
		var initGraph =  function (graph) {
			$.ajax({
				type : "POST",
				url : "index.php?_a=1",
				data: {
					action : "initGraph"
				},
				dataType : "json",
				success : function(data){
					if(data.status!==600){
						console.log(data.message);
						return;
					}
					$(data.nodedata).each(function(){
						if(this.level !== 0){
							createNode.apply(this,[{
								graph: graph,
								posX: this.posX,
								posY: this.posY,
								nodeId: this.level
							}]);
						}
					});
				},
				error : function(){
					
				}
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
			//set the default action
			graph.nodeAction = "select";
			
			graph.isCtrl=false;

			$(document).bind({
				"keydown":function(e){
					graph.isCtrl = e.which === 17;
				},
				"keyup": function(e){
					graph.isCtrl = e.which === 17;
				}
				
			})
			
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
								posY: data.posY,
								nodeId: data.nodeId
							}]);
						}
					});
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