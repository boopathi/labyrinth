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
				//set circle details
				$("#addNode input[name=posX]").val(e.x);
				$("#addNode input[name=posY]").val(e.y);
				$("#nodeEditor").slideDown(150);
				
				return false;
			});
			
			graph.actionasd = function(opts) {
				switch(opts.nodeAction){
						case "edit":
							this.fill="#abcdef";
							this.redraw();
							var that = this;
							$.ajax({
								type: "POST",
								url: "index.php?_a=1",
								data : {
									action : "showNode",
									level : ""
								},
								dataType : "json" ,
								success : function (data){
									if(data.status != 600){
										console.log(data.message);
										return false;
									}
									$("#nodeEditor .content").html(data.html);
								},
								error : function (){
									
								}
							})
							opts.parentArgs.onNodeEdit.apply(this,[function(){
								//end of edit
								that.fill = "#fff";
								that.redraw();
							}]);
							break;
						case "delete":
							opts.parentArgs.onNodeDelete.apply(this,[]);
							break;
						case "path":
							opts.parentArgs.onNodePath[node_num++].apply(this,[]);
							break;
					}
			}//end of actionasd function
			
			window.graph = graph;
		});
	}
})(jQuery, this, this.document);


var first_node="";
(function($,window,docment,undefined){
	//extend jQuery
	$("#addNode").submit(function(e){
		e.preventDefault();
		$(this).ajaxSubmit({
			dataType:"json",
			success: function(response){
				if(response.status != 600){
					console.log(response.message);
					return false;
				}
				var graph = window.graph;
				var node = graph.display.arc({
					x:response.posX, 
					y:response.posY,
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
					switch ($("#actionType select").val()){
						case "editnode":
							first_node="";
							break;
						case "addpath":
							if(first_node=="" || first_node == this.id){
								console.log(this.id);
								first_node = this.id;
							}
							else{
								console.log("second node");
								
								
								$("#pathEditor").slideDown(150);
								first_node="";
							}							
							break;
						case "removepath":
							first_node="";
							break;
						case "removenode":
							first_node="";
							break;
					}
					
				});
				graph.nodes.push(node);
				graph.addChild(node);
				$("#nodeEditor").slideUp(150);
			}
		});
		return false;
	});
	
	$("#removeNode").submit(function(e){
		e.preventDefault();
		$(this).ajaxSubmit({
			dataType:"json",
			success: function(response){
				console.log(response);
			}
		});
		return false;
	});
	
	$("#addPath").submit(function(e){
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
		onNodeEdit: function(callback){
			console.log(this);
			$("#nodeEditor").slideDown(150);
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