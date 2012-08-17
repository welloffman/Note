/**
 * Класс для работы с записью
 * @param {Object} data - данные для записи
 */
function Note(data) {
	var ob = this;

	var id = data.id;
	var dir_id = data.dir_id;
	var title = data.title;
	var content = data.content;
	var row = null;
	
	var selected = false;
	var open = false;

	/**
	 * Формирует вид записи и выводит ее на страницу
	 */
	ob.build = function() {
		row = $("#tpl_explorer_row tr").clone();
		row.addClass("note_row");
		row.attr("n_id", id);
		row.find(".row_title").append("<p>" + title + "</p>");
		row.find(".sel_check").addClass("note_check");
		$("#explorer tbody").append(row);
	}

	/**
	 * Назначает действие для клика по строке соответствующей записи
	 */
	ob.bindAction = function() {
		if(row == null) return;

		$(row.find(".row_title > p")).on('click', function(){
			cancel();
			show();

			// Кнопка редактора "Отмена"
			$("#tiny_mce_form input[name='reset']").off("click").on("click", function() {
				cancel();
				show();
			});

			// Кнопка сохранить
			$("#tiny_mce_form input[name='save']").off("click").on("click", function() {
				save();
			});
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

	/**
	 * Сбрасывает текстовый редактор
	 */
	function cancel() {
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
	function show() {
		if(!open) {
			$.post("/notes/getNoteContent", {id: id}, function(result) {
				content = $.parseJSON(result);

				var content_row = $("<div class='h'></div>").html(content);
				content_row.off("dblclick").on("dblclick", function() {
					onEditMode();
				});
				
				row.find(".row_title").append(content_row);
				row.find(".row_title .h").slideDown("slow");
			});
		}
		else {
			row.find(".row_title .h").slideUp("slow", function() {
				row.find(".h").remove();
			});
		}
		
		open = !open;
	}

	/**
	 * Сохраняет запись
	 */
	function save() {
		tinymce.activeEditor.save();
		var data = {
			id: id,
			parent_dir: dir_id,
			title: title,
			content: $("#mce").val()
		};

		$.post("/notes/editNote", data, function(result) {
			var resp = $.parseJSON(result);

			cancel();
			show();
		});
	}

	/**
	 * Отображает редактор контента
	 */
	function onEditMode() {
		var elem = row.find(".h");
		$(elem).off("click");
		$("#mce").val(content);

		$(elem).empty();
		$(elem).append( $("#tiny_mce_form") );

		tinyMCE.execCommand("mceAddControl", false, "mce");
	}
}