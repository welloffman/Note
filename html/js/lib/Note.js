/**
 * Класс для работы с записью
 * @param {Object} data - данные для записи
 */
function Note(data) {
	var ob = this;
	
	var id = data.id;
	var title = data.title;
	var selected = false;
	var row = makeRow();
	var dir_id = data.dir_id;
	var content = data.content;
	var open = false;
	
	ob.pos = data.pos === null ? null : data.pos * 1;
	ob.type = "Note";
	
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
	 * Формирует дом объект - строку для записи
	 */
	function makeRow() {
		var row = $("#tpl_explorer_row li").clone();
		row.addClass("note_row");
		row.attr("n_id", id);
		row.find(".row_title").append("<p>" + title + "</p>");
		return row;
	}
	
	/**
	 * Выводит запись на страницу
	 */
	ob.render = function() {
		$("#explorer").append(row);
	}

	/**
	 * Сбрасывает текстовый редактор
	 */
	ob.cancel = function() {
		// Отключаем текстовый редактор
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		// Переносим форму редактора в скрытый блок
		$("#blocks").append($("#tiny_mce_form"));
		// Удаляем ненужную строку
		$("#note").remove();
	}

	/**
	 * Отображает контент записи
	 */
	ob.showContent = function() {
		if(!open) {
			$.post("/notes/getNoteContent", {id: id}, function(result) {
				content = $.parseJSON(result);

				var content_row = $("<div class='h'></div>").html(content);
				content_row.off("dblclick").on("dblclick", function(e) {
					ob.onEditMode();
				});
				
				row.find(".row_title").append(content_row);
				row.find(".row_title .h").slideDown("slow");
				row.addClass("opened");
			});
		}
		else {
			row.find(".row_title .h").slideUp("slow", function() {
				row.find(".h").remove();
				row.removeClass("opened");
			});
		}
		
		open = !open;
	}

	/**
	 * Сохраняет запись
	 */
	 ob.save = function() {
		tinymce.activeEditor.save();
		var data = {
			id: id,
			parent_dir: dir_id,
			title: title,
			content: $("#mce").val()
		};

		$.post("/notes/editNote", data, function(result) {
			var resp = $.parseJSON(result);

			ob.cancel();
			ob.showContent();
		});
	}

	/**
	 * Отображает редактор контента
	 */
	ob.onEditMode = function() {
		var elem = row.find(".h");
		$(elem).off("click");
		$("#mce").val(content);

		$(elem).empty();
		$(elem).append( $("#tiny_mce_form") );

		tinyMCE.execCommand("mceAddControl", false, "mce");
	}
}