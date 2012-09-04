/* 
 * Класс - родитель для добавления записей и разделов
 */

function Add() {
		
	var refresh;
	
	/**
	 * Отображает контент по умолчанию
	 * @params {Boolean} update Указатель обновлять или нет список загруженных разделов или записей
	 */
	this.defaultState = function(update) {
		var content = $( "#" + this.control.attr("tab_content") );
		content.parent().children().hide();
		
		if(update) refresh( $("#parent_dir_name").val() );
		
		content.parent().children().first().show();
		
	}
	
	/**
	 * Событие клика на кнопку таба
	 */
	this.bindAction = function() {
		var ob = this;
		// Кнопка отображающая таб
		$(this.control).off('click').on('click', function() {
			ob.init();
			
			var content = $( "#" + ob.control.attr("tab_content") );
			content.parent().children().hide();
			content.show();
		});
	}
	
	/**
	 * Назначает функцию для обновления данных
	 * @param {Function} func Функция для обновления данных
	 */
	this.setRefresh = function(func) {
		refresh = func;
	}
}

/**
 * Класс для добавления раздела
 * @param {Jquery Object} elem Кнопка по которой будет выводиться интерфейс для добавления раздела
 */
function AddDir(elem) { 
	var ob = this;
	
	ob.control = elem;
	
	ob.init = function() {};
	
	ob.bindAction();
	
	// Кнопка добавить раздел
	$("#create_dir").off('click').on('click', function() {
		var data = {
			parent_dir: $("#parent_dir_name").val(),
			title: $("#tab_add_dir input[name='dir_name']").val()
		};

		$.post("/notes/addDir", data, function(result) {
			var resp = $.parseJSON(result);
			ob.defaultState(true);
		});
	});
	
	$( "#" + elem.attr("tab_content") ).find(".cancel").off('click').on('click', function() {
		ob.defaultState();
	});
}
AddDir.prototype = new Add();

/**
 * Класс для добавления записи
 * @param {Jquery Object} elem Кнопка по которой будет выводиться интерфейс для добавления записи
 */
function AddNote(elem) {
	var ob = this;
	
	ob.control = elem;
	
	ob.init = function() {
		$("#tab_add_note input[name='note_name']").val("");
		
		tinyMCE.execCommand('mceRemoveControl', false, "mce2");
		$("#mce2").val("");
		tinyMCE.execCommand('mceRemoveControl', false, "mce");
		$("#mce").val("");
		$("#blocks").append($("#tiny_mce_form"));
		
		tinyMCE.execCommand("mceAddControl", false, "mce2");
	}
	
	ob.bindAction();
	
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

			ob.defaultState(true);
		});
	});
	
	$( "#" + elem.attr("tab_content") ).find(".cancel").off('click').on('click', function() {
		ob.defaultState();
	});
}
AddNote.prototype = new Add();