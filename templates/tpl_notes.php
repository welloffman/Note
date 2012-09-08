<div id="container">
	<div id="sidebar">
		<ul id="panel">
			<li class="b_note" tab_content="tab_notes" title="Блокнот"></li>
			<li class="b_add_dir" tab_content="tab_add_dir" title="Добавить раздел"></li>
			<li class="b_add_note" tab_content="tab_add_note" title="Добавить запись"></li>
			<li class="b_edit_title" title="Изменить заголовок"></li>
			<li class="b_copy" title="Копировать"></li>
			<li class="b_cut" title="Вырезать"></li>
			<li class="b_paste" title="Вставить"></li>
			<li class="b_delete" title="Удалить"></li>
		</ul>
	</div>
	<div id="content">
		<div id="title_begin">
			<div>
				<p id="dir_title"></p><span id="title_end"></span>
			</div>
		</div>
		
		<ul class="breadcrumb"></ul>
		
		<div>
			<!-- Список разделов и записей -->
			<div id="tab_notes" class="table_container">					
				<ul id="explorer">
				</ul>
			</div>

			<!-- Добавление нового раздела -->
			<div id="tab_add_dir">
				<form class="form-horizontal">
					<fieldset>
                        <legend>Новый раздел</legend>
						<div class="control-group">
							<label class="control-label">Родительский раздел</label>
							<div class="controls">
								<select class="input-xlarge" id="parent_dir_name">
								</select>
								<p class="help-block">Выберите раздел, в котором хотите создать новый</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Название раздела</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="dir_name" />
								<p class="help-block">Введите название для нового раздела</p>
							</div>
						</div>
						<div class="form-actions">
							<button type="button" class="btn btn-primary" id="create_dir">Создать</button>
							<button type="button" class="btn cancel">Отмена</button>
						</div>
					</fieldset>
				</form>
			</div>

			<!-- Добавление новой записи -->
			<div id="tab_add_note">
				<form class="form-horizontal">
					<fieldset>
                        <legend>Новая запись</legend>
						<div class="control-group">
							<label class="control-label">Родительский раздел</label>
							<div class="controls">
								<select class="input-xlarge" id="parent_dir_name2">
								</select>
								<p class="help-block">Выберите раздел, в котором хотите создать запись</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Заголовок записи</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="note_name" />
								<p class="help-block">Введите название новой записи</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Текст записи</label>
							<div class="controls">
								<textarea id="mce2" name="content"></textarea>
								<br />
								<input class="btn" type="button" id="save_note_button" value="Сохранить" />
								<button type="button" class="btn cancel">Отмена</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
			
		
	</div>
	
	
	
	
	

</div>

<!-- Блоки для создания динамически формируемого контента -->
<div id="blocks">

	<!-- Окно для текстового редактора -->
	<form id="tiny_mce_form" method="post" action="/">
		<textarea id="mce" name="content"></textarea>
		<br />
		<input class="btn" type="button" name="save" value="Сохранить" />
		<input class="btn" type="button" name="reset" value="Отмена" />
	</form>
	
	<!-- Шаблон строки раздела или записи -->
	<ul id="tpl_explorer_row">
		<li>
			<div class="dragable"></div>
			<div class="row_title"></div>
			<div class="sel_check"></div>
		</li>
	</table>
</div>

<script src="/js/lib/Modal.js"></script>
<script src="/js/lib/CopyCut.js"></script>
<script src="/js/lib/NotesPage.js"></script>
<script src="/js/lib/ListItem.js"></script>
<script src="/js/lib/Dir.js"></script>
<script src="/js/lib/Note.js"></script>
<script src="/js/lib/Add.js"></script>
<script src="/js/lib/Iterator.js"></script>
<script src="/js/lib/Breadcrumbs.js"></script>
<script src="/js/lib/tiny_mce/tiny_mce.js"></script>
<script src="/js/notes_page.js"></script>
