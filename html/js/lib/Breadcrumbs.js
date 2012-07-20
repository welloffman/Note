/**
 * Класс для работы с хлебными крошками
 * @param {Array} dirs_data - Массив данных по хлебным крошкам
 */
function Breadcrumbs(dirs_data) {
	var ob = this;

	var data = dirs_data;

	/**
	 * Построение хлебных крошек
	 */
	ob.build = function() {
		$(".breadcrumb li").remove();

		var max = data.length;
		for(var i = 0; i < max; i++) {
			var crumb = data[i];
			if(i < max - 1) var li = $('<li><a href="#" n_id="' + crumb.id + '" title="' + crumb.title + '">' + cropCrumb(crumb.title) + '</a><span class="divider">/</span></li>')
			else li = $("<li title='" + crumb.title + "'>" + cropCrumb(crumb.title) + "</li>" );
			$(".breadcrumb").append(li);
		}
	}

	/**
	 * Клик по хлебным крошкам
	 * @param {Function} action - функция срабатывающая при клике
	 */
	ob.bindActions = function(action) {
		$(".breadcrumb a").off('click').on('click', function(){
			action( $(this).attr("n_id") );
			return false;
		});
	}
}