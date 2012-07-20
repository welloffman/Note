$("document").ready(function() {
	init_navbar();
});

/**
 * Инициализация верхнего меню (выделение текущей страницы)
 */
function init_navbar() {
	var path = document.location.pathname.match(/^\/\w*/)[0];
	$(".nav-collapse a[href='" + path + "']").parent().addClass("active");
}