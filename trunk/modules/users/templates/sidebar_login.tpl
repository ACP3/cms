<ul class="nav nav-list">
	<li class="nav-header">{lang t="users|login"}</li>
	<li>
		<form action="{uri args="users/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8" class="form-inline" style="text-align:center">
			<input type="text" name="nickname" id="nav-nickname" maxlength="30" class="input-small" placeholder="{lang t="users|nickname"}" required style="width:40%">
			<input type="password" name="pwd" id="nav-pwd" class="input-small" placeholder="{lang t="users|pwd"}" required style="width:40%">
			<div style="margin:5px 0">
				<label for="nav-remember" class="checkbox">
					<input type="checkbox" name="remember" id="nav-remember" value="1">
					{lang t="users|remember_me"}
				</label>
			</div>
			<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		</form>
	</li>
	<li class="divider"></li>
	<li><a href="{uri args="users/forgot_pwd"}">{lang t="users|forgot_pwd"}</a></li>
{if $enable_registration == 1}
	<li><a href="{uri args="users/register"}">{lang t="users|register"}</a></li>
{/if}
</ul>