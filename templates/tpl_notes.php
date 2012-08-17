<div id="container">
	<div id="sidebar">
		<ul id="panel">
			<li class="b_add_dir"></li>
			<li class="b_add_note"></li>
			<li class="b_order"></li>
			<li class="b_edit_title"></li>
			<li class="b_copy"></li>
			<li class="b_cut"></li>
			<li class="b_paste"></li>
			<li class="b_delete"></li>
		</ul>
	</div>
	<div id="content">
		<div id="title_begin">
			<div>
				<p id="dir_title"></p><span id="title_end"></span>
			</div>
		</div>
		
		<ul class="breadcrumb"></ul>
		
		<div class="table_container">
			<table id="explorer">
				<tbody></tbody>
			</table>
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
			<td class="row_title"></td>
			<td class="check_cell">
				<div class="sel_check"></div>
			</td>
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
