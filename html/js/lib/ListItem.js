/** 
 * Класс родитель для записей и разделов
 */

function ListItem() {
	var id;
	var title;
	var row = null;
	var selected = false;
	
	this.isSelect = function() {
		return this.selected;
	};
	
	/**
	 * Навешивает событие на элемент
	 * 
	 * @param {String} selector Css селектор на дом элемент
	 * @param {Function} func Функция
	 * @param {Multiply} params Параметры для функции
	 */
	this.bindAction = function(func, selector, params) {
		this.row.find(selector).off('click').on('click', function(event) {
			func(this, params, event);
		});
	}
}