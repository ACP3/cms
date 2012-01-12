{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset style="width:30%">
		<legend>{lang t="users|login"}</legend>
		<dl>
			<dt><label for="nickname">{lang t="users|nickname"}</label></dt>
			<dd><input type="text" name="form[nickname]" id="nickname" maxlength="30"></dd>
			<dt><label for="pwd">{lang t="users|pwd"}</label></dt>
			<dd><input type="password" name="form[pwd]" id="pwd"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<label for="remember">
			<input type="checkbox" name="remember" id="remember" value="1" class="checkbox">
			{lang t="users|remember_me"}
		</label>
		<br>
		<br>
		<input type="submit" value="{lang t="common|submit"}" class="form">
	</div>
</form>