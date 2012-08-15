/**
 * Класс для страници "Записи"
 * @param {Object} data - данные по странице
 */
function NotesPage(data) {
	var ob = this;

	var breadcrumbs = null;
	var tabs = new TabsIterator();
	var dirs = new ElemIterator();
	var notes = new ElemIterator();

	var modal = new Modal();
	
	var copy_cut = new CopyCut();

	/**
	 * Инициализация хлебных крошек
	 */
	function initBreadcrumbs() {
		if(dirs == null) initDirs();
		breadcrumbs = new Breadcrumbs(data.crumbs, ob);
		breadcrumbs.build();
		breadcrumbs.bindActions(function(id) {
			ob.refresh(id);
			tabs.getById("tab_list").select();
		});
	}

	/**
	 * Инициализация табов
	 */
	function initTabs() {
	   /**
		* Обработка таба "записи"
		*/
		var tabNotes = function() {
			resetEditor();
			initDirs();
			initNotes();
		}

		/**
		* Обработка таба "Добавить раздел"
		*/
		var tabAddDir = function() {
			$("#tab_add_dir input[name='dir_name']").val("");
			
			// Кнопка добавить раздел
			$("#create_dir").off('click').on('click', function() {
				var data = {
					parent_dir: $("#parent_dir_name").val(),
					title: $("#tab_add_dir input[name='dir_name']").val()
				};

				$.post("/notes/addDir", data, function(result) {
					var resp = $.parseJSON(result);

					// Обновляем содержимое директории
					ob.refresh(resp.dir_id);

					// Переходим в таб "Просмотр записей"
					tabs.getById("tab_list").select();
					tabNotes();
				});
			});
		}

		/**
		* Обработка таба "Добавить записи"
		*/
		var tabAddNote = function() {
			$("#tab_add_note input[name='note_name']").val("");
			resetEditor();
			tinyMCE.execCommand("mceAddControl", false, "mce2");

			// Кнопка добавить запись
			$("#save_note_button").off('click').on('click', function() {
				tinymce.activeEditor.save();
				var params = {
					parent_dir: $("#parent_dir_name2").val(),
					title: $("#tab_add_note input[name='note_name']").val(),
					content: $("#mce2").val()
				};

				$.post("/notes/addNote", params, function(result) {
					var resp = $.parseJSON(result);

					// Обновляем содержимое директории
					ob.refresh(resp.dir_id);

					// Переходим в таб "Просмотр записей"
					var tab = tabs.getById("tab_list");
					tab.select();
					tabNotes();
				});
			});
		}

		// Связываем формируем параметры для инициализации табов
		var items = [
			{func: tabNotes, element: $('.nav-tabs a[href="#tab_notes"]').parent()},
			{func: tabAddDir, element: $('.nav-tabs a[href="#tab_add_dir"]').parent()},
			{func: tabAddNote, element: $('.nav-tabs a[href="#tab_add_note"]').parent()}
		];

		// Инициализируем табы
		for(var i in items) {
			var t = new Tab(items[i].element, items[i].func);
			tabs.add(t);
		}
	}

	/**
	 * Инициализация разделов
	 */
	function initDirs() {
		$("#explorer tbody").empty();
		dirs.clear();

		for(var i in data.dirs_data) {
			var d = new Dir(data.dirs_data[i]);
			d.build();
			d.bindActions(ob.refresh);
			dirs.add(d);
		}
	}

	/**
	 * Инициализация записей
	 */
	function initNotes() {
		notes.clear();

		for(var i in data.notes_data) {
			var n = new Note(data.notes_data[i]);
			n.build();
			n.bindAction();
			notes.add(n);
		}
	}

	/**
	 * Обновление содержимого страницы
	 */
	ob.refresh = function(dir_id) {
		if(dir_id == undefined || dir_id == "") return;

		var params = {
			dir_id: dir_id
		};

		$.post("/notes/refreshDir", params, function(result) {
			data = $.parseJSON(result);

			$("#blocks").append( $("#controls_but") );

			resetEditor();
			$("#dir_title").html(data.dir_title);
			initBreadcrumbs();
			initDirs();
			initNotes();
			initListToAddDir(data.crumbs);
		});
	}

	/**
	 * Инициализация списков разделов (разделы входящие в хлебные крошки)
	 * @param {Array} data - массив с параметрами крошек
	 */
	function initListToAddDir(data) {
		$("#parent_dir_name").empty();
		$("#parent_dir_name2").empty();
		if(data.length == 0) data.unshift({id: "", title: "Главный раздел"});

		for(var i in data) {
			var item = data[i];
			var option = $('<option value="' + item.id + '" title="' + item.title + '">' + cropCrumb(item.title) + '</option>')
			$("#parent_dir_name").append( option.clone() );
			$("#parent_dir_name2").append(option);
		}
		$("#parent_dir_name :last-child").attr("selected", "selected");
		$("#parent_dir_name2 :last-child").attr("selected", "selected");
	}

	/**
	 * Инициализация кнопок для редактирования заголовков
	 */
	function initButtons() {
		
		// Кнопка изменить
		$("#controls .edit").on("click", function() {
			var cur_dir = dirs.getSelected();
			var cur_note = notes.getSelected();
			
			if(cur_dir.length > 0) {
				var params = {
					type: "editDirTitle",
					dir: cur_dir[0]
				}
			}
			else if(cur_note.length > 0) {
				params = {
					type: "editNoteTitle",
					note: cur_note[0]
				}
			}
			else return;
			
			modal.setData(params);
			modal.show(ob.refresh);
		});

		// Кнопка копировать
		$("#controls .copy").on("click", function() {
			copy_cut.setAction("copy");
			copy_cut.add(notes, dirs);
			$("#controls .paste").removeClass("disabled");
		});

		// Кнопка вырезать
		$("#controls .cut").on("click", function() {
			copy_cut.setAction("cut");
			copy_cut.add(notes, dirs);
			$("#controls .paste").removeClass("disabled");
		});
		
		// Кнопка вставить
		$("#controls .paste").on("click", function() {
			var options = copy_cut.getOptions();
			// Так определяем ид родителя
			options.new_parent_id = data.crumbs[data.crumbs.length - 1].id;
			
			if(options.dirs.length == 0 && options.notes.length == 0) return;
			
			$.post("/notes/" + copy_cut.getAction(), options, function(result) {
				var data = $.parseJSON(result);
				
				copy_cut.clear();
				ob.refresh(options.new_parent_id);
				
				$("#controls .paste").addClass("disabled");
			});
		});

		// Кнопка удалить
		$("#controls .delete").on("click", function() {
			var params = {
				type: "del",
				notes: notes.getSelected(),
				dirs: dirs.getSelected(),
				// Так определяем ид родителя
				parent_id: data.crumbs[data.crumbs.length - 1].id
			}
			
			if(params.dirs.length > 0 || params.notes.length > 0) {
				modal.setData(params);
				modal.show(ob.refresh);
			}
		});
	}
	
	/**
	 * Сбрасывает редактор на состояние по умолчанию
	 */
	function resetEditor() {
		tinyMCE.execCommand('mceRemoveControl', false, "mce2");
		$("#mce2").val("");
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		$("#mce").val("");
		$("#blocks").append($("#tiny_mce_form"));
	}

	initBreadcrumbs();
	$("#dir_title").html(data.dir_title);
	initTabs();
	initDirs();
	initNotes();
	initListToAddDir(data.crumbs);

	initButtons();
}