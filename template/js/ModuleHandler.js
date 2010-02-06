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
 * Code by: Eldhose C G | http://ceegees.in  | eldhose (at) ceegees [dot] in
 * Initiated by: Dennis Hettema    |    http://hette.ma    |     hettema (at) gmail [dot] com
 */

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

