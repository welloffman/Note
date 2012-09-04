/**
 * Класс для работы с разделом
 * @param {Object} data - данные для раздела
 */
function Dir(data) {
	var ob = this;

	var id = data.id;
	var title = data.title;
	var selected = false;
	var parent_id = data.parent_id;
	var row = makeRow();
	
	ob.pos = data.pos === null ? null : data.pos * 1;
	ob.type = "Dir";

	/**
	 * Выводит раздел на страницу
	 */
	ob.render = function() {
		$("#explorer").append(row);
	}
	
	ob.getId = function() {
		return id;
	}
	
	ob.getTitle = function() {
		return title;
	}
	ob.setTitle = function(value) {
		title = value;
	}
	
	ob.getSelected = function() {
		return selected;
	}
	ob.setSelected = function(val) {
		selected = val;
	}
	
	/**
	 * Формирует дом объект - строку для раздела
	 */
	function makeRow() {
		var row = $("#tpl_explorer_row li").clone();
		row.addClass("dir_row");
		row.attr("n_id", id);
		row.find(".row_title").append("<p>" + title + "</p>");
		return row;
	}
}

Dir.prototype = new ListItem();