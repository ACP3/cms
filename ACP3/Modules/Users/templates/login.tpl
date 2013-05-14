{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="nickname" class="control-label">{lang t="users|nickname"}</label>
		<div class="controls"><input type="text" name="nickname" id="nickname" maxlength="30" required></div>
	</div>
	<div class="control-group">
		<label for="pwd" class="control-label">{lang t="users|pwd"}</label>
		<div class="controls"><input type="password" name="pwd" id="pwd" required></div>
	</div>
	<div class="control-group">
		<div class="controls">
			<label for="remember" class="checkbox">
				<input type="checkbox" name="remember" id="remember" value="1">
				{lang t="users|remember_me"}
			</label>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn"><i class="icon-lock"></i> {lang t="users|log_in"}</button>
		<a href="{uri args="users/forgot_pwd"}" class="btn btn-link">{lang t="users|forgot_pwd"}</a>
	</div>
</form>