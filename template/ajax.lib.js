function serialize(a){
	var s = [];
	if(a.constructor == Array)
		for(var i=0;i<a.length;i++)
			s.push( a[i].name + "=" + encodeURIComponent(a[i].value) );
	else
		for(var j in a)
			s.push( j + "=" + encodeURIComponent(a[j]) );
	return s.join('&');
}
var ajax = function ( options ) {
	options = {
		type: options.type || "POST",
		url: options.url || "",
		timeout: options.timeout || 5000,
		onStart: options.onStart || function() {},
		onComplete: options.onComplete || function(){},
		onError: options.onError || function(){},
		onSuccess: options.onSuccess || function(){},
		data: options.data || "",
		responseType: options.responseType || "json",
		async: options.async || true,
	};
	if( typeof options.data != "string")
		options.data = this.serialize(options.data);
	if( options.type.toUpperCase() == "GET" )
		options.url += "?" + options.data;
	
	var xml = new XMLHttpRequest();
	options.onStart();
	xml.open(options.type, options.url, options.async);
	xml.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var timeoutLength = options.timeout;
	var requestDone = false;
	setTimeout(function(){
	requestDone = true;
	}, timeoutLength);
	
	function httpSuccess(r) {
		try {
			return !r.status && location.protocol == "file:" ||
				( r.status >= 200 && r.status < 300 ) ||
				r.status == 304 ||
				navigator.userAgent.indexOf("Safari") >= 0
				&& typeof r.status == "undefined";
		} catch(e){}
		return false;
	}
	function httpData(r,type) {
		var ct = r.getResponseHeader("content-type");
		var data = !type && ct && ct.indexOf("xml") >= 0;
		data = type == "xml" || data ? r.responseXML : r.responseText;
		if ( type == "script" )
		eval.call( window, data );
		return data;
	}
	
	xml.onreadystatechange = function(){
		if ( xml.readyState == 4 && !requestDone ) {
			if ( httpSuccess( xml ) ) {
				if(xml.getResponseHeader("Content-Type").indexOf(options.responseType) >=0 ) {
					var resp = httpData( xml, options.type );
					options.onSuccess(resp);
				}
				else {
					options.onError(httpData(xml, options.type));
				}
			} else {
				options.onError("HTTP_FAILURE:"+ xml.status);
			}//endif
			options.onComplete();
			//destroy the request object
			xml = null;
		}
	};
	//send data
	xml.send(options.type.toUpperCase()=="POST" ? options.data : null);
	
};
