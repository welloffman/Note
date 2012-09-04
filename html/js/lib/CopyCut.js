/**
 * Класс для работы с переносом разделов и записей
 */
function CopyCut() {
	var ob = this;
	
	// Хранит действие (копировать или вырезать)
	var action = "";
	
	// Хранит массивы с id записей и разделов
	var options = {
		dirs: [],
		notes: []
	}
	
	/**
	 * Заполняет параметры значениями id записей и разделов
	 * @param {ElemIterator} elems Объект - итератор записей и разделов
	 */
	ob.setOptions = function(elems) {
		options.dirs = [];
		options.notes = [];

		var sel_items = elems.getSelected();
		for(var i in sel_items) {
			if(sel_items[i].type == "Dir") options.dirs.push(sel_items[i].getId());
			else if(sel_items[i].type == "Note") options.notes.push(sel_items[i].getId());
		}
	}
	
	/**
	 * Назначает тип действия
	 * @param {string} act Тип действия
	 */
	ob.setAction = function(act) {
		if(act == "copy" || act == "cut") action = act;
	}
	
	ob.clear = function() {
		action = "";
		options = {
			dirs: [],
			notes: []
		}
	}
	
	ob.getOptions = function() {
		return options;
	}
	
	ob.getAction = function() {
		return action;
	}
	
	ob.count = function() {
		return options.dirs.length + options.notes.length;
	}
}