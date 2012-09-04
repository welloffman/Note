/**
 * Класс для работы с записью
 * @param {Object} data - данные для записи
 */
function Note(data) {
	var ob = this;

	ob.dir_id = data.dir_id;
	ob.content = data.content;
	ob.open = false;
	ob.id = data.id;
	ob.title = data.title;ob.row = null;
	ob.selected = false;
	ob.row = makeRow();
	
	ob.bindAction(clickNote, ".row_title > p");

	/**
	 * Выводит запись на страницу
	 */
	ob.render = function() {
		$("#explorer tbody").append(ob.row);
	}
	
	/**
	 * Клик по заголовку записи
	 */
	function clickNote() {
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
	}
	
	/**
	 * Формирует дом объект - строку для записи
	 */
	function makeRow() {
		var row = $("#tpl_explorer_row tr").clone();
		row.addClass("note_row");
		row.attr("n_id", ob.id);
		row.find(".row_title").append("<p>" + ob.title + "</p>");
		row.find(".sel_check").addClass("note_check");
		return row;
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
		if(!ob.open) {
			$.post("/notes/getNoteContent", {id: ob.id}, function(result) {
				ob.content = $.parseJSON(result);

				var content_row = $("<div class='h'></div>").html(ob.content);
				content_row.off("dblclick").on("dblclick", function() {
					onEditMode();
				});
				
				ob.row.find(".row_title").append(content_row);
				ob.row.find(".row_title .h").slideDown("slow");
				ob.row.addClass("opened");
			});
		}
		else {
			ob.row.find(".row_title .h").slideUp("slow", function() {
				ob.row.find(".h").remove();
				ob.row.removeClass("opened");
			});
		}
		
		ob.open = !ob.open;
	}

	/**
	 * Сохраняет запись
	 */
	function save() {
		tinymce.activeEditor.save();
		var data = {
			id: ob.id,
			parent_dir: ob.dir_id,
			title: ob.title,
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
		var elem = ob.row.find(".h");
		$(elem).off("click");
		$("#mce").val(ob.content);

		$(elem).empty();
		$(elem).append( $("#tiny_mce_form") );

		tinyMCE.execCommand("mceAddControl", false, "mce");
	}
}

Note.prototype = new ListItem();