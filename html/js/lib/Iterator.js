/**
 * Класс - итератор для объектов
 */
function MyIterator() {
	var ob = this;

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

	ob.data = [];
	ob.current = null;

	/**
	 * Возвращает элемент по id
	 * @param {String} id - идентификатор объекта
	 */
	ob.getById = function(id) {
		for(var i in ob.data) {
			if(ob.data[i].getId() == id) return ob.data[i];
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
			if(elem && elem.isSelect()) elems.push(elem);
		} while(elem != null);
		return elems;
	}
}
ElemIterator.prototype = new MyIterator();

/**
 * Класс - итератор табов
 */
function TabsIterator() {
	var ob = this;

	ob.data = [];
	ob.current = null;

	/**
	 * Возвращает таб по цсс идентификатору
	 * @param {String} id - цсс идентификатор
	 */
	ob.getById = function(id) {
		for(var i in ob.data) {
			if( $(ob.data[i].getElem()).attr("id") == id ) return ob.data[i];
		}
		return null;
	}
}
TabsIterator.prototype = new MyIterator();