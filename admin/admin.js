//Plugin Labyrinth Admin
(function($,window,document,undefined){
	$.fn.labygraph = function(options){
		//default settings
		options = $.extend({
			background: "#222",
			fps: 60
		},options);
		//make it available for cascading
		return this.each(function(){
			var graph = oCanvas.create({
				canvas: "#"+this.id,
				background: options.background,
				fps: options.fps
			});
			graph.nodes=[];
			var isChild = false;
			graph.bind("click", function(e){
				//draw a circle
				e.preventDefault();
				if(isChild)return;
				var node = graph.display.arc({
					x:e.x, y:e.y,
					radius: 5,
					start: 0, end: 360,
					fill: "#fff"
				});
				node.bind("mouseenter", function(evt){
					this.radius=7;
					this.redraw();
				}).bind("mouseleave", function(evt){
					this.radius=5;
					this.redraw();
				}).bind("click", function(evt){
					isChild=true;
					//TODO: this is not working properly. Remove isChild and timedout call
					evt.stopPropagation();
					evt.preventDefault();
					console.log("node clicked");
					//call the edit node function
					setTimeout(function(){
						isChild = false;
					}, 100);
				});
				console.log("canvas clicked");
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
	$("#addNode, #removeNode, #addPath, #removePath").ajaxForm({
		dataType: "json",
		success: function(response){
			console.log(response);
		}
	});

	$("#graph").labygraph();
	
})(jQuery,this,this.document);