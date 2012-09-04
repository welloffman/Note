<div class="container">
	<form method="post" action="/register/reg" class="login">
		<p>
			<label for="login">Логин:</label>
			<input type="text" name="login" id="login" placeholder="Логин">
		</p>
		<p>
			<label for="password">Пароль:</label>
			<input type="password" name="passwd" id="password" placeholder="Пароль">
		</p>
		<p class="login-submit">
			<button type="submit" class="login-button">Отправить</button>
		</p>
		<div class="tip">
			<p>Рекомендуется в качестве логина использовать адрес электронной почты для возможности восстановления забытого пароля.</p>
			<p>Длинна пароля от 6 до 20 символов.</p>
		</div>
	</form>
</div>
<script src="/js/register.js"></script>