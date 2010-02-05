/*
 * Class for handling interaction with a REST Module.
 */
function ModuleHandler(moduleName,list,add, value)  {
	var listAccessor = list;
	var valueAccessor = value;
	var addAccessor = add;
	var moduleName = moduleName;
	
	this.getValue = function() {
		var selectedValue = $(listAccessor).val();
		if (!selectedValue || selectedValue == "") {
			return;
		} 
		gTransactoinManager.process('get',moduleName, selectedValue);
	}
	this.removeItem = function() {
		var selectedValue = $(listAccessor).val();
		gTransactoinManager.process('remove',moduleName, selectedValue);
		$(valueAccessor).val("\n");
	}
	this.addItem = function() {
		var newItem = $(addAccessor).val();
		gTransactoinManager.process('add',moduleName, newItem);
		$(addAccessor).val('');
	}
	this.updateValue = function() {
		var selectedValue = $(listAccessor).val();
		gTransactoinManager.process('update',moduleName, selectedValue, 
		{data:$(valueAccessor).val()});
	}
	this.reloadList = function(result) {
		var list = $(listAccessor);
		list.children().remove();
		for (var idx = 0; idx < result.listValues.length; idx++) {
			$(listAccessor).append('<option value="'+result.listValues[idx]+'">'+result.listValues[idx]+'</option>');
		}
	},
	this.handle = function(result) {
		if (result.op == 'add') {
			gTransactoinManager.process('get',moduleName);
		} else if(result.op == 'get' && result.listValues) {
			this.reloadList(result);
			this.getValue();
			
		} else if(result.op == 'get' && typeof result.value == 'string') {
			$(valueAccessor).val(result.value);
		} else if(result.op == 'update' ) {
			showDialog('Value updated');
		} else if(result.op == 'remove' ) {
			gTransactoinManager.process('get',moduleName);
			showDialog('Removed Item and corresponding value');
		}
	}
}

