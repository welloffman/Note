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
	 * @param {ElemIterator} notes Объект - итератор записей
	 * @param {ElemIterator} dirs Объект - итератор разделов
	 */
	ob.add = function(notes, dirs) {
		options.dirs = [];
		options.notes = [];

		var sel_dirs = dirs.getSelected();
		for(var i in sel_dirs) {
			options.dirs.push(sel_dirs[i].id);
		}

		var sel_notes = notes.getSelected();
		for(i in sel_notes) {
			options.notes.push(sel_notes[i].id);
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
}