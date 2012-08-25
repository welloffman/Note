/**
 * Класс для работы с разделом
 * @param {Object} data - данные для раздела
 */
function Dir(data) {
	var ob = this;

	ob.id = data.id;
	ob.title = data.title;
	ob.selected = false;
	ob.parent_id = data.parent_id;
	ob.row = makeRow();

	/**
	 * Выводит раздел на страницу
	 */
	ob.render = function() {
		$("#explorer tbody").append(ob.row);
	}
	
	/**
	 * Формирует дом объект - строку для раздела
	 */
	function makeRow() {
		var row = $("#tpl_explorer_row tr").clone();
		row.addClass("dir_row");
		row.attr("n_id", ob.id);
		row.find(".row_title").append("<p>" + ob.title + "</p>");
		row.find(".sel_check").addClass("dir_check");
		return row;
	}
}

Dir.prototype = new ListItem();