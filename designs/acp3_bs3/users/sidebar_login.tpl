<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="users|login"}</h3>
	</div>
	<div class="panel-body">
		<form action="{uri args="users/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8">
			<div class="form-group">
				<input class="form-control" type="text" name="nickname" id="nav-nickname" maxlength="30" placeholder="{lang t="users|nickname"}" required>
			</div>
			<div class="form-group">
				<input class="form-control" type="password" name="pwd" id="nav-pwd" placeholder="{lang t="users|pwd"}" required>
			</div>
			<div class="checkbox">
				<label for="nav-remember">
					<input type="checkbox" name="remember" id="nav-remember" value="1">
					{lang t="users|remember_me"}
				</label>
			</div>
			<button type="submit" name="submit" class="btn btn-primary"><i class="glyphicon glyphicon-lock"></i> {lang t="users|log_in"}</button>
		</form>
		<div class="list-group" style="margin:10px 0 0">
			<a href="{uri args="users/forgot_pwd"}" class="list-group-item"><i class="glyphicon glyphicon-question-sign"></i> {lang t="users|forgot_pwd"}</a>
			{if $enable_registration == 1}
				<a href="{uri args="users/register"}" class="list-group-item"><i class="glyphicon glyphicon-star"></i> {lang t="users|register"}</a>
			{/if}
		</div>
	</div>
</div>