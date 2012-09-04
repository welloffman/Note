/**
 * Класс для страници "Записи"
 * @param {Object} data - данные по странице
 */
function NotesPage(data) {
	var ob = this;

	var breadcrumbs = null;
	
	var dirs = new ElemIterator();
	var notes = new ElemIterator();
	
	/**
	 * Обновление содержимого страницы
	 * @param {Object} el ДОМ элемент по оторому кликнули (нужен для совместимости с интерфейсом функции навешивания событий)
	 * @param {Number} dir_id Ид раздела
	 */
	ob.refresh = function(el, dir_id) {
		if(dir_id == undefined || dir_id == "") return;

		var params = {
			dir_id: dir_id
		};

		$.post("/notes/refreshDir", params, function(result) {
			
			// todo: здесь этому не место
			/*$(".b_edit_title").css("margin-left", "");
			$(".b_copy").css("margin-left", "");
			$(".b_cut").css("margin-left", "");
			$(".b_paste").css("margin-left", "");
			$(".b_delete").css("margin-left", "");*/
			
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
	
	var add_dir = new AddDir( $(".b_add_dir"), dirs );
	add_dir.setRefresh(ob.refresh);
	var add_note = new AddNote( $(".b_add_note"), notes );
	add_note.setRefresh(ob.refresh);
	
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
			ob.refresh(null, id);
		});
	}

	/**
	 * Инициализация разделов
	 */
	function initDirs() {
		$("#explorer tbody").empty();
		dirs.init("Dir", data.dirs_data);
		
		dirs.reset();
		var dir = dirs.get();
		while(dir) {
			dir.bindAction(ob.refresh, ".row_title", dir.id);
			dir.bindAction(selectCheckbox, ".sel_check", dir);
			dir = dirs.get();
		}
	}

	/**
	 * Инициализация записей
	 */
	function initNotes() {
		notes.init("Note", data.notes_data);
		
		notes.reset();
		var note = notes.get();
		while(note) {
			note.bindAction(selectCheckbox, ".sel_check", note);
			note = dirs.get();
		}
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
		$("#panel .b_edit_title").on("click", function() {
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
		$("#panel .b_copy").on("click", function() {
			copy_cut.setAction("copy");
			copy_cut.add(notes, dirs);
			$("#controls .paste").removeClass("disabled");
		});

		// Кнопка вырезать
		$("#panel .b_cut").on("click", function() {
			copy_cut.setAction("cut");
			copy_cut.add(notes, dirs);
			$("#controls .paste").removeClass("disabled");
		});
		
		// Кнопка вставить
		$("#panel .b_paste").on("click", function() {
			var options = copy_cut.getOptions();
			// Так определяем ид родителя
			options.new_parent_id = data.crumbs[data.crumbs.length - 1].id;
			
			if(options.dirs.length == 0 && options.notes.length == 0) return;
			
			$.post("/notes/" + copy_cut.getAction(), options, function(result) {
				var data = $.parseJSON(result);
				
				copy_cut.clear();
				ob.refresh(null, options.new_parent_id);
				
				$("#controls .paste").addClass("disabled");
			});
		});

		// Кнопка удалить
		$("#panel .b_delete").on("click", function() {
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
	 * Обработчик клика по чекбоксу
	 * @param {Object} el ДОМ элемент по оторому кликнули
	 * @param {Multiply} ob Текущий элемент
	 * @param {Object} event Событие
	 */
	function selectCheckbox(el, ob, event) {
		ob.selected = !ob.selected;
		
		if(ob.selected) {
			/*selected_total++;
			if(selected_total == 1) buttons("show");*/
			
			$(el).append($("<div />", {"class": "jackdaw"}));
			$(el).parent().parent().css("background-color", "#f0f0f0");
		}
		else {
			/*selected_total--;
			if(selected_total == 0) buttons("hide");*/
			
			$(el).empty();
			$(el).parent().parent().css("background-color", "");
		}
		event.stopPropagation();
	}
	
	/**
	 * Отображает / скрывает кнопки редактирования
	 * @param {String} act Указатель на дейсвие отображать или скрывать
	 */
	function buttons(act) {
		if(act == "show") {
			var margin = 45;
			var step = -1;
		}
		else {
			margin = 0;
			step = 1;
		}
		
		
		var descriptor = setInterval( function() {
			margin += step;

			if(margin < 0 || margin > 45) {
				clearInterval(descriptor);
				return;
			}

			$(".b_edit_title").css("margin-left", margin + "px");
			$(".b_copy").css("margin-left", margin + "px");
			$(".b_cut").css("margin-left", margin + "px");
			$(".b_paste").css("margin-left", margin + "px");
			$(".b_delete").css("margin-left", margin + "px");
		}, 5);
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
	initDirs();
	initNotes();
	initListToAddDir(data.crumbs);

	initButtons();
}

// todo: Убрать это отсюда!!!
//selected_total = 0;