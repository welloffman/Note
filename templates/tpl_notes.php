<div class="container">
	<div class="row">
		<div class="span12">
			<ul class="breadcrumb">
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="span12">

			<ul class="nav nav-tabs">
				<li id="tab_list" class="active"><a href="#tab_notes" data-toggle="tab">Список записей</a></li>
				<li id="tab_new_dir"><a href="#tab_add_dir" data-toggle="tab">Добавить раздел</a></li>
				<li id="tab_new_note"><a href="#tab_add_note" data-toggle="tab">Добавить запись</a></li>
			</ul>

			<div id="myTabContent" class="tab-content">

				<!-- Список разделов и записей -->
				<div id="tab_notes" class="tab-pane fade in active">					
					<table id="explorer" class="table table-striped">
						<div id="controls">
							<button class="btn edit" title="Изменить" data-toggle="modal" href="#editTitleModal">
								<i class="icon-pencil"></i>
							</button>
							<!--a class="btn" data-toggle="modal" href="#myModal" >Launch Modal</a-->
							<button class="btn copy">Копировать</button>
							<button class="btn cut">Вырезать</button>
							<button class="btn paste disabled">Вставить</button>
							<button class="btn btn-danger delete" title="Удалить"><i class="icon-trash icon-white"></i></button>
						</div>
						<tbody></tbody>
					</table>
				</div>

				<!-- Добавление нового раздела -->
				<div class="tab-pane fade" id="tab_add_dir">
					<form class="form-horizontal">
						<fieldset>
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
							</div>
						</fieldset>
					</form>
				</div>

				<!-- Добавление новой записи -->
				<div class="tab-pane fade" id="tab_add_note">
					<form class="form-horizontal">
						<fieldset>
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
								</div>
							</div>
						</fieldset>
					</form>
				</div>

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
	<table id="tpl_explorer_row">
		<tr>
			<td class="check_cell">
				<input type="checkbox" class="sel_check" value="" />
			</td>
			<td class="row_title"></td>
		</tr>
	</table>
</div>

<script src="/js/lib/Modal.js"></script>
<script src="/js/lib/CopyCut.js"></script>
<script src="/js/lib/NotesPage.js"></script>
<script src="/js/lib/Dir.js"></script>
<script src="/js/lib/Note.js"></script>
<script src="/js/lib/Iterator.js"></script>
<script src="/js/lib/Breadcrumbs.js"></script>
<script src="/js/lib/Tab.js"></script>
<script src="/js/lib/tiny_mce/tiny_mce.js"></script>
<script src="/js/notes_page.js"></script>
