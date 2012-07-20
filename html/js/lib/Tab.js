/**
 * Класс для работы с табами страницы записи
 * @param {DomElement} elem - DomElement таба
 * @param {Function} act - ф-ция - обработчик нажатия таба
 */

function Tab(elem, act) {
	var ob = this;

	var tab = elem;
	var action = act;

	/**
	 * Выбор таба (активизация)
	 */
	ob.select = function() {
		$(elem).find("a").tab('show');
	}

	/**
	 * Клик по табу
	 */
	function bindActions() {
		$(tab).off("click").on("click", function() {
			action();
			$(this).find("a").tab('show');
			return false;
		});
	}

	ob.getElem = function() {
		return elem;
	}
	
	bindActions();
}