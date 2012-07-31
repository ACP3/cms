<h4>Login</h4>
<form action="{$uri}" method="post" accept-charset="UTF-8" id="login-sidebar">
	<fieldset>
		<dl>
			<dt><label for="nav-nickname">{lang t="users|nickname"}</label></dt>
			<dd><input type="text" name="nickname" id="nav-nickname" maxlength="30" required></dd>
		</dl>
		<dl>
			<dt><label for="nav-pwd">{lang t="users|pwd"}</label></dt>
			<dd><input type="password" name="pwd" id="nav-pwd" required></dd>
		</dl>
	</fieldset>
	<div>
		<label for="nav-remember">
			<input type="checkbox" name="remember" id="nav-remember" value="1" class="checkbox">
			{lang t="users|remember_me"}
		</label>
		<p style="text-align:center">
			<input type="hidden" name="redirect_uri" value="{$redirect_uri}">
			<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		</p>
		<a href="{uri args="users/forgot_pwd"}">{lang t="users|forgot_pwd"}</a>
{if $enable_registration == 1}
		<br>
		<a href="{uri args="users/register"}">{lang t="users|register"}</a>
{/if}
	</div>
</form>