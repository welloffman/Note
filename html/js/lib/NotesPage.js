/**
 * Класс - контроллер для страницы "Записи"
 * @param {Object} data - данные для страницы
 */
function NotesPage(data) {
	var ob = this;

	/**
	 * Переменная в которой будем хранить объект для работы с хлебными крошками
	 */
	var breadcrumbs = null;
	
	/**
	 * Итератор содержащий объекты для работы с разделами и записями
	 */
	var elems = new ElemIterator();
	
	/**
	 * Объект для работы с копированием и переносом разделов и записей
	 */
	var copy_cut = new CopyCut();
	
	/**
	 * Объект для работы с табом "Добавить раздел"
	 */
	var add_dir = new AddDir( $(".b_add_dir") );
	add_dir.setRefresh(refresh);
	
	/**
	 * Объект для работы с табом "Добавить запись"
	 */
	var add_note = new AddNote( $(".b_add_note") );
	add_note.setRefresh(refresh);
	
	/**
	 * Объект для работы с модальными окнами
	 */
	var modal = new Modal();
	
	/**
	 * Обновление содержимого страницы
	 * @param {Number} dir_id Ид раздела
	 */
	function refresh(dir_id) {
		if(dir_id == undefined || dir_id == "") return;

		var params = {
			dir_id: dir_id
		};

		$.post("/notes/refreshDir", params, function(result) {
			
			if(copy_cut.count() > 0) setMarginEditButtons(0);
			else setMarginEditButtons(45);
			
			data = $.parseJSON(result);

			$("#blocks").append( $("#controls_but") );

			resetEditor();
			$("#dir_title").html(data.dir_title);
			initBreadcrumbs();
			$("#explorer").empty();
			initElems();
			initListToAddDir(data.crumbs);
		});
	}
	
	/**
	 * Инициализация хлебных крошек
	 */
	function initBreadcrumbs() {
		breadcrumbs = new Breadcrumbs(data.crumbs);
		breadcrumbs.build();
		breadcrumbs.bindActions(function(id) {
			refresh(id);
		});
	}
	
	/**
	 * Инициализация разделов и записей
	 */
	function initElems() {
		elems.clear();

		for(var i in data.dirs_data) {
			var d = new Dir(data.dirs_data[i]);
			elems.add(d);
		}
		
		for(var i in data.notes_data) {
			var n = new Note(data.notes_data[i]);
			elems.add(n);
		}
		
		elems.reset();
		elems.sort();
		var item = elems.get();
		while(item) {
			item.render();
			item = elems.get();
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

			setMarginEditButtons(margin);
		}, 5);
	}
	
	function setMarginEditButtons(val) {
		var margin = val + "px";
		$(".b_edit_title").css("margin-left", margin);
		$(".b_copy").css("margin-left", margin);
		$(".b_cut").css("margin-left", margin);
		$(".b_paste").css("margin-left", margin);
		$(".b_delete").css("margin-left", margin);
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
	
	/**
	 * Запоминает позицию элемента после сортировки
	 * @param {Jquery.event} event Событие
	 * @param {Object} ui Объект с различными данными ui
	 */
	function sort(event, ui) {
		var resp_data = [];
		$.each(ui.item.parent().children(), function(i, el){
			var elem = $(el);
			resp_data.push({pos: i, id: elem.attr("n_id"), type: elem.attr("class")});
		});
		
		$.post("/notes/sort", {sort_data: resp_data}, function(result) {
			
		});
	}
	
	
	
// =============================================================================
// Инициализация при первой загрузке страницы
// =============================================================================

	initBreadcrumbs();
	$("#dir_title").html(data.dir_title);
	initElems();
	initListToAddDir(data.crumbs);	
	
	/**
	 * Делаем элементы сортируемыми
	 */
	$("#explorer").sortable({
		placeholder: "ui-state-highlight",
		opacity: 0.8,
		axis: 'y',
		containment: "#explorer",
		distance: 10,
		handle: ".dragable",
		tolerance: "pointer",
		stop: sort
	});	
	
	
	
// =============================================================================
// Клики по элементам страницы
// =============================================================================
	
	/**
	 * Клик по чекбоксам 
	 */
	$("#explorer").on("click", ".sel_check", function(e){
		var row = $(this).parent();
		var type = row.attr("class").replace("_row", "");
		var id = row.attr("n_id");
		var elem = elems.getItem(id, type);
		
		elem.setSelected( !elem.getSelected() );
		
		if(elem.getSelected()) {
			$(this).addClass("checked");
			if($(".checked").length == 1 && copy_cut.count() == 0) buttons("show");
		}
		else {
			$(this).removeClass("checked");
			if($(".checked").length == 0 && copy_cut.count() == 0) buttons("hide");
		}
		e.stopPropagation();
	});
	
	/**
	 * Клик по разделам 
	 */
	$("#explorer").on("click", ".dir_row", function() {
		refresh( $(this).attr("n_id") );
	});
	
	/**
	 * Блокируем клик по блоку для перетаскивания 
	 */
	$("#explorer").on("click", ".dragable", function(e) {
		e.stopPropagation();
	});
	
	/**
	 * Блокируем клик по развернутой записи 
	 */
	$("#explorer").on("click", ".h", function(e) {
		e.stopPropagation();
	});
	
	/**
	 * Клик по записям 
	 */
	$("#explorer").on("click", ".note_row", function() {
		var note = elems.getItem( $(this).attr("n_id"), "Note" );
		
		note.cancel();
		note.showContent();

		// Кнопка редактора "Отмена"
		$("#tiny_mce_form input[name='reset']").off("click").on("click", function() {
			note.cancel();
			note.showContent();
		});

		// Кнопка сохранить
		$("#tiny_mce_form input[name='save']").off("click").on("click", function() {
			note.save();
		});
	
	});
	
	// Кнопка изменить 
	$("#panel .b_edit_title").on("click", function() {
		var edited = elems.getSelected();

		if(edited.length > 0) {
			
			if(edited[0].type == "Dir") var act_type = "editDirTitle";
			else if(edited[0].type == "Note") act_type = "editNoteTitle";
			else return;
			
			var params = {
				type: act_type,
				item: edited[0]
			}
		}
		
		modal.setData(params);
		modal.show(refresh);
	});

	// Кнопка копировать 
	$("#panel .b_copy").on("click", function() {
		copy_cut.setAction("copy");
		copy_cut.setOptions(elems);
	});

	// Кнопка вырезать 
	$("#panel .b_cut").on("click", function() {
		copy_cut.setAction("cut");
		copy_cut.setOptions(elems);
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
			refresh(options.new_parent_id);

			buttons("hide");
		});
	});

	// Кнопка удалить 
	$("#panel .b_delete").on("click", function() {
		var params = {
			type: "del",
			del_elems: elems.getSelected(),
			// Так определяем ид родителя
			parent_id: data.crumbs[data.crumbs.length - 1].id
		}

		if(params.del_elems.length > 0) {
			modal.setData(params);
			modal.show(refresh);
		}
	});
}