(function($,window,docment,undefined){
	//extend jQuery
	var actions = {
		addNode: function(options){
			
		},
		removeNode: function(options){
			$.extend({
				
			},options);
		},
		addPath: function(options){
			$.extend({
				
			},options);
		},
		removePath: function(options){
			$.extend({
				
			},options);
		}
	};
	$.extend({
		labyrinth: function(action, options){
			actions[action].apply(this, [options]);
		}
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