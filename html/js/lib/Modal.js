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
		$("#customModal .modal_edit").show();
		$("#customModal .modal_edit .modal-header h3").html("Изменение заголовка раздела");
		$("#customModal .modal_edit .modal-body").empty();
		$("#customModal .modal_edit .modal-body").append($("<input />", {
			type: "text",
			"class": "title_str",
			value: data.dir.getTitle()
		}));

		// Событие на кнопку сохранить
		$("#customModal .modal_edit .ok").off("click").on("click", function() {
			data.dir.setTitle($("#customModal .title_str").val());
			$.post("/notes/renameDir", {id: data.dir.getId(), title: data.dir.getTitle()}, function(result) {
				$('#customModal').modal('hide');
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
			value: data.note.getTitle()
		}));

		// Событие на кнопку сохранить
		$("#customModal .modal_edit .ok").off("click").on("click", function() {
			data.note.setTitle($("#customModal .title_str").val());
			$.post("/notes/renameNote", {id: data.note.getId(), title: data.note.getTitle()}, function(result) {
				$('#customModal').modal('hide');
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
		$("#customModal .modal_delete").show();
		
		var list_dirs = "";
		var dir_ids = [];
		for(var i in data.dirs) {
			var item = data.dirs[i];
			list_dirs += item.getTitle() + "<br />";
			dir_ids.push(item.getId());
		}
		
		var list_notes = "";
		var note_ids = [];
		for(i in data.notes) {
			item = data.notes[i];
			list_notes += item.getTitle() + "<br />";
			note_ids.push(item.getId());
		}
		
		var body_message = "";
		if(list_dirs) body_message += "<h4>Будут удалены следующие разделы:</h4><p>" + list_dirs + "</p>";
		if(list_notes) body_message += "<h4>Будут удалены следующие записи:</h4><p>" + list_notes + "</p>";
		
		$("#customModal .modal_delete .modal-body").empty();
		$("#customModal .modal_delete .modal-body").html(body_message);
		
		// Событие на кнопку удалить
		$("#customModal .modal_delete .ok").off("click").on("click", function() {
			$.post("/notes/deleteItem", {dirs: dir_ids, notes: note_ids}, function(result) {
				$('#customModal').modal('hide');
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
		$('#customModal').modal({show: true, keyboard: true});

	}

	/**
	 * Передает параметры
	 */
	ob.setData = function(params) {
		data = params;
	}
}