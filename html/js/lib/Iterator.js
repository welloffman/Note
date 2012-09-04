/**
 * Класс - итератор для объектов
 */
function Iterator() {

	/**
	 * Добавляет элемент в конец массива данных итератора и ставит на него указатель
	 * @param {Object} item - объект
	 */
	this.add = function(item) {
		this.data.push(item);
		this.current = this.data.length - 1;
	}

	/**
	 * Возвращает текуший объект из массива данных итератора и сдвигает указатель на 1 вперед
	 * при достижении конца массива возвращает null
	 */
	this.get = function() {
		if(this.current == null) return null;

		if(this.current < this.data.length) var item = this.data[this.current];
		else {
			item = null;
			this.current = null;
		}
		this.current++;

		return item;
	}

	/**
	 * Сбрасывает указатель на начало массива
	 */
	this.reset = function() {
		this.current = 0;
	}

	/**
	 * Возвращает длинну массива объектов
	 * @return {Number}
	 */
	this.size = function() {
		return this.data.lenght;
	}

	/**
	 * Очищает массив
	 */
	this.clear = function() {
		this.data = [];
		this.current = null;
	}
}



/**
 * Класс - итератор для разделов и записей
 */
function ElemIterator() {
	var ob = this;

	var sort_by = "custom";

	ob.data = [];
	ob.current = null;
	

	/**
	 * Возвращает элемент по id
	 * @param {String} id - идентификатор объекта
	 */
	ob.getById = function(id) {
		for(var i in ob.data) {
			if(ob.data[i].id == id) return ob.data[i];
		}
		return null;
	}
	
	/**
	 * Возвращает элемент по id и типу
	 * @param {Integer} id
	 * @param {String} type
	 */
	ob.getItem = function(id, type) {
		for(var i in ob.data) {
			var item = ob.data[i];
			if(item.getId() == id && item.type.toLowerCase() == type.toLowerCase()) 
				return ob.data[i];
		}
		return null;
	}
	
	/**
	 * Возвращает массив с выделенными элементами
	 * @return {Array}
	 */
	ob.getSelected = function() {
		var elems = [];
		ob.reset();
		do {
			var elem = ob.get();
			if(elem && elem.getSelected()) elems.push(elem);
		} while(elem != null);
		return elems;
	}
	
	/**
	 * Формирует масив элементов для итератора
	 * @param {String} type Тип элементов для итератора
	 * @param {Array} data Массив с данными для элементов
	 */
	ob.init = function(type, data) {
		ob.clear();

		for(var i in data) {
			var d = factory(type, data[i]);
			d.render();
			ob.add(d);
		}
	}
	
	ob.sort = function() {
		if(sort_by == "tytle") sortByTytle();
		else if(sort_by =="type") sortByType();
		else {
			
			var no_pos = [];
			var with_pos = [];
			for(var i in ob.data) {
				var item = ob.data[i];
				if(item.pos === null) no_pos.push(item);
				else with_pos.push(item);
			}
			ob.data = with_pos;
			
			if(ob.data.length > 0) sortByPos(ob.data, 0, with_pos.length - 1);
			ob.data = ob.data.concat(no_pos);
		}
	}
	
	function sortByTytle() {
		
	}
	
	function sortByType() {
		
	}
	
	/**
	 * Сортирует записи и разделы методом быстрой сортировки по позиции
	 */
	function sortByPos(arr, low_index, high_index) {
		var i = low_index;
		var j = high_index;
		var middle = Math.floor((low_index + high_index) / 2);
		var x = ob.data[middle].pos;
		do {
			while(ob.data[i].pos < x) ++i;
			while(ob.data[j].pos > x) --j;
			if(i <= j){
				var temp = ob.data[i];
				ob.data[i] = ob.data[j];
				ob.data[j] = temp;
				i++; j--;
			}
		} while(i <= j);

		if(low_index < j) sortByPos(arr, low_index, j);
		if(i < high_index) sortByPos(arr, i, high_index);
	}
	
	/**
	 * Фабрика объектов для итератора
	 * @param {String} type Тип элементов для итератора
	 * @param {Array} data Массив с данными для элемента
	 */
	function factory(type, data) {
		if(type == "Dir") return new Dir(data);
		else if(type == "Note") return new Note(data);
		else return null;
	}
	
	
}
ElemIterator.prototype = new Iterator();