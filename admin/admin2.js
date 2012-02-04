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
						console.log(self);
						//append the loaded html and resize the image
						$("#viewNode").html(data.html).css({
							top: $("#graph").offset().top + self.posY - 3,
							left: $("#graph").offset().left + self.posX + 10
						}).find("img").css({
							width: 100, height: 100
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
				
			},
			"createPath": function() {
				
			}
		}
		
		var createNode = function(options){
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
				if(graph.isCtrl && !graph.isAlt){
					//then the first node is selected
					$("#addPath input[name=from]").val(this.qno);
				} else if(graph.isAlt && !graph.isCtrl){
					if($("#addPath input[name=from]").val() == null)
						console.log("First Select the starting node");
					else
						$("#addPath input[name=to]").val(this.qno);
					//continue with updating the db. show a textbox
					$("#showTextBox").css({
						top: $("#graph").offset().top + self.posY - 3,
						left: $("#graph").offset().left + self.posX + 10
					}).show();
				} else {
					var _tselector = $("#actionType select[name=actionType]").val();
					if(typeof _tselector === "undefined")
						return;
					handlerObject[_tselector].apply(this,[]);	
				}
			});
			graph.nodes.push(node);
			graph.addChild(node);
		}
		//make it available Globally, to use it as an API
		window.labygraph.createNode = createNode;
		
		var initGraph =  function () {
			$.ajax({
				type : "POST",
				url : "index.php?_a=1",
				data: {
					action : "initGraph"
				},
				dataType : "json",
				success : function(data){
					
				},
				error : function(){
					
				}
			});
			//createNode();
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
			
			graph.isCtrl=false;
			graph.isAlt=false;
			$(document).bind({
				"keydown":function(e){
					graph.isCtrl = e.which === 17;
					graph.isAlt = e.which === 18;
				},
				"keyup": function(e){
					graph.isCtrl = e.which === 17;
					graph.isAlt = e.which === 18;
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