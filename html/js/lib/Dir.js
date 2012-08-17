/**
 * Класс для работы с разделом
 * @param {Object} data - данные для раздела
 */
function Dir(data) {
	var ob = this;

	var id = data.id;
	var parent_id = data.parent_id;
	var title = data.title;
	var row = null;
	var selected = false;

	/**
	 * Формирует вид раздела и выводит ее на страницу
	 */
	ob.build = function() {
		row = $("#tpl_explorer_row tr").clone();
		row.addClass("dir_row");
		row.attr("n_id", id);
		row.find(".row_title").append("<p>" + title + "</p>");
		row.find(".sel_check").addClass("dir_check");
		$("#explorer tbody").append(row);
	}

	/**
	 * Назначает действие для клика по строке соответствующей разделу и чекбоксу
	 * @param {Function} refresh_function - Функция выполняющаяся при клике на раздел
	 */
	ob.bindActions = function(refresh_function) {
		if(row == null) return;

		row.find(".row_title").on('click', function(){
			refresh_function(id);
		});
		
		$(row).find(".sel_check").off('click').on('click', function(event) {
			selected = !selected;
			if(selected) $(this).append($("<div />", {"class": "jackdaw"}));
			else $(this).empty();
			event.stopPropagation();
		});
	}

	ob.getId = function() {
		return id;
	}

	ob.getTitle = function() {
		return title;
	}
	
	ob.isSelect = function() {
		return selected;
	};

	ob.setTitle = function(string) {
		title = string;
	}
}