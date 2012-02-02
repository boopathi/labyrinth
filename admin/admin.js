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
	
	//create the canvas object
	window.graph = oCanvas.create({
		canvas: "#graph",
		background: "#222",
		fps: 60
	});
	
	/*
	$.fn.labyrinth = function(options){
		//default settings
		$.extend({
			action: null,
			from: null,
			to: null,
			key: null,
			questionHtml: null,
			
		},options);
		return this.each(function(){
			
		});
	}*/
})(jQuery,this,this.document);
