$("document").ready(function() {
	
	// Нажатие на кнопку "отправить" в форме авторизации
	$("form.log_form input[type='button']").bind('click', function() {
		var data = {
			"login": $("form.log_form input[name='login']").val(),
			"passwd": $("form.log_form input[name='passwd']").val()
		};
		
		$.post("/Register/reg", data, function(result) {
			var resp = $.parseJSON(result);
			if(resp.result == "ok") window.location.href = "/notes";
			
		});
		return false;
	});
	
});
