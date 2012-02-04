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
	/*.ajaxForm({
		dataType: "json",
		success: function(response){
			console.log(response);
		}
	});*/
	
	//create the canvas object
	window.graph = oCanvas.create({
		canvas: "#graph",
		background: "#222",
		fps: 60
	});
})(jQuery,this,this.document);