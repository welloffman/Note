/**
 * Класс для работы с модальным окном
 */
function Modal() {
	var ob = this;
	var data = {};

	// Событие после отображения окна
	$('#customModal').off('shown').on('shown', function () {
		$(".title_str").focus();
	})

	/**
	 * Формирование окна для редактирования заголовка раздела
	 * @param {Function} action - действие совершаемое после нажатия кнопки сохранить
	 */
	var editDirTytle = function(action) {
		$("#customModal > div").hide();
		
		$("#customModal .modal_edit .modal-header h3").html("Изменение заголовка раздела");
		$("#customModal .modal_edit .modal-body").empty();
		$("#customModal .modal_edit .modal-body").append($("<input />", {
			type: "text",
			"class": "input-large title_str",
			value: data.item.getTitle()
		}));
		$("#customModal .modal_edit").show();

		// Событие на кнопку сохранить
		$("#customModal .modal_edit .ok").off("click").on("click", function() {
			data.item.setTitle( $("#customModal .title_str").val() );
			$.post("/notes/renameDir", {id: data.item.getId(), title: data.item.getTitle()}, function(result) {
				$("#customModal").hide("slow");
				var resp = $.parseJSON(result);
				if(resp) action(resp);
			});
		});
	}

	/**
	 * Формирование окна для редактирования заголовка записи
	 * @param {Function} action - действие совершаемое после нажатия кнопки сохранить
	 */
	var editNoteTytle = function(action) {
		$("#customModal > div").hide();
		$("#customModal .modal_edit").show();
		$("#customModal .modal_edit .modal-header h3").html("Изменение заголовка записи");
		$("#customModal .modal_edit .modal-body").empty();
		$("#customModal .modal_edit .modal-body").append($("<input />", {
			type: "text",
			"class": "title_str",
			value: data.item.getTitle()
		}));

		// Событие на кнопку сохранить
		$("#customModal .modal_edit .ok").off("click").on("click", function() {
			data.item.setTitle( $("#customModal .title_str").val() );
			$.post("/notes/renameNote", {id: data.item.getId(), title: data.item.getTitle()}, function(result) {
				$("#customModal").hide("slow");
				var resp = $.parseJSON(result);
				if(resp) action(resp);
			});
		});
	}
	
	/**
	 * Формирование окна для удаления записей и разделов
	 * @param {Function} action - действие совершаемое после нажатия кнопки сохранить
	 */
	var del = function(action) {
		$("#customModal > div").hide();
		
		var list_dirs = "";
		var list_notes = "";
		var dir_ids = [];
		var note_ids = [];
		for(var i in data.del_elems) {
			var item = data.del_elems[i];
			if(item.type == "Dir") {
				list_dirs += item.getTitle() + "<br />";
				dir_ids.push(item.getId());
			}
			else if(item.type == "Note") {
				list_notes += item.getTitle() + "<br />";
				note_ids.push(item.getId());
			}
		}
		
		var body_message = "";
		if(list_dirs) body_message += "<h4>Будут удалены следующие разделы:</h4><p>" + list_dirs + "</p>";
		if(list_notes) body_message += "<h4>Будут удалены следующие записи:</h4><p>" + list_notes + "</p>";
		
		$("#customModal .modal_delete .modal-body").empty();
		$("#customModal .modal_delete .modal-body").html(body_message);
		$("#customModal .modal_delete").show();
		
		// Событие на кнопку удалить
		$("#customModal .modal_delete .ok").off("click").on("click", function() {
			$.post("/notes/deleteItem", {dirs: dir_ids, notes: note_ids}, function(result) {
				$("#customModal").hide("slow");
				action(data.parent_id);
			});
		});
	}

	/**
	 * Акцептор для запуска формирования разных типов окон
	 */
	var acceptors = {
		editDirTitle: editDirTytle,
		editNoteTitle: editNoteTytle,
		del: del
	}

	/**
	 * Запускает формирование и отображает модальное окно
	 * @param {Function} action - действие совершаемое после нажатия кнопки сохранить
	 */
	ob.show = function(action) {
		acceptors[data.type](action);
		$("#mask").css("height", $("body").height());
		$("#customModal").show("slow");

	}

	/**
	 * Передает параметры
	 */
	ob.setData = function(params) {
		data = params;
	}
	
	
	
	// Событие на кнопку отмена
	$("#customModal .modal .dismiss").on("click", function() {
		$("#customModal").hide("slow");
	});
	
	// Событие на кнопку закрыть
	$("#customModal .modal .close").on("click", function() {
		$("#customModal").hide("slow");
	});
}