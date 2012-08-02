$("document").ready(function() {

	var resp = $.parseJSON( $("#json_block").html() );
	
	$("#login").attr("value", resp.login);
	if(resp.email) $("#mail").attr("value", resp.email);
	
	// Клик по кнопке сохранить
	$(".form-actions .btn").on('click', function() {
		var data = {
			login: $("#login").attr("value"),
			email: $("#mail").attr("value"),
			new_pass: $("#newpass").attr("value"),
			old_pass: $("#oldpass").attr("value")
		};
		$.post("/settings/change", data, function(result) {
			var resp = $.parseJSON(result);
			var msg = new OptionsMessager(resp.result);
			msg.showMessage();
			
		});
		
		return false;
	});
});

/**
 * Класс для вывода сообщений от формы настроек
 */
function OptionsMessager(error) {
	var ob = this;
	
	var success_block = $("<div class='alert alert-success'>Готово</div>");
	
	switch (error) {
		case "oldpass_invalid" :
			var block_name = "oldpass";
			var message = "Пароль не верен!";
			break;
		case "login_alredy_used" :
			block_name = "login";
			message = "Логин уже используется!";
			break;
		case "login_invalid" :
			block_name = "login";
			message = "Недопустимый логин!";
			break;
		case "newpass_invalid" :
			block_name = "newpass";
			message = "Недопустимый пароль!";
			break;
		default :
			block_name = "";
			message = "";
	}
	
	/**
	 * Отображение сообщения
	 */
	ob.showMessage = function() {
		clear();

		if(!block_name) { 
			success();
		}
		else {
			var msg_block = $("<span class='help-inline'>" + message + "</span>");

			var block = $("." + block_name);
			var prev_elem = $("#" + block_name);

			block.addClass("error");
			msg_block.insertAfter(prev_elem);
		}
	}
	
	/**
	 * Очистка формы от старых сообщений
	 */
	function clear() {
		$(".control-group").removeClass("error");
		$(".help-inline").remove();
	}
	
	function hideSuccess() {
		success_block.fadeOut("slow", success_block.remove);
	}
	
	function success() {
		success_block.insertAfter($(".leg_header"));
		
		setTimeout(hideSuccess, 2000);
	}
}