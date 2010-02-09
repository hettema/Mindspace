/**
 * Copyright 2009, 2010 hette.ma.
 * 
 * This file is part of Mindspace.
 * Mindspace is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.Mindspace is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.You should have received a copy of the GNU General Public License
 * along with Mindspace. If not, see <http://www.gnu.org/licenses/>.
 * 
 *  credits
 * ----------
 * Idea by: Garrett French |    http://ontolo.com    |     garrett <dot> french [at] ontolo (dot) com
 * Code by: Alias Eldhose| http://ceegees.in  | eldhose (at) ceegees [dot] in
 * Initiated by: Dennis Hettema    |    http://hette.ma    |     hettema (at) gmail [dot] com
 */
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