$("document").ready(function() {

	var resp = $.parseJSON( $("#json_block").html() );
	var np = new NotesPage(resp);

	// Инициализация текстового редактора
	tinyMCE.init({
		mode: "none",
		theme: "advanced",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,|,bullist,numlist,|,undo,redo,|,forecolor,backcolor",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_buttons4 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
		height : "600"
	});
});

/**
 * Обрезает заголовок
 * @param {String} title - заголовок
 */
function cropCrumb(title) {
	if(title.length > 15) return title.slice(0, 12) + "...";
	else return title;
}