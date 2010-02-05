
var gTransactoinManager = function() {

	var moduleHandlers = [];
	return {
		process : function( op,module,value, dataObj) {
			try {
			if (!value) {
				value = '';
			}
			var method = "GET";
			var pData = "";
			for (key in dataObj) {
				pData += key +"=" + encodeURIComponent(dataObj[key])+"&";
			}
			if (dataObj != null) {
				method = "POST";
			}
			var url = "data.php?op="+op+"&mod="+module+"&value="+value;
			
			$.ajax({
	   			type: method,
	   			url: url,
	   			data:pData,
	   			success: function(res){
	   				var result;
	     			eval('result='+res);
					if(result.success) {
						moduleHandlers[result.module].handle(result);
					} else {
						showDialog(result.message);
					}
	     			
	   			}
 			});
 			
			} catch(e){
				showDialog(e);
			}
		}
		,
		registerModuleHandler : function(name,handler) {
			moduleHandlers[name] = handler;
		}
	};
	
}();