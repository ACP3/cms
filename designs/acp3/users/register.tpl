{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="users|nickname"} &amp; {lang t="common|email"}</a></li>
			<li><a href="#tab-2">{lang t="users|pwd"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="nickname">{lang t="users|nickname"}</label></dt>
				<dd><input type="text" name="form[nickname]" id="nickname" value="{$form.nickname}" maxlength="30"></dd>
				<dt><label for="mail">{lang t="common|email"}</label></dt>
				<dd><input type="email" name="form[mail]" id="mail" value="{$form.mail}" maxlength="120"></dd>
			</dl>
		</div>
		<div id="tab-2" clas="ui-tabs-hide">
			<dl>
				<dt><label for="pwd">{lang t="users|pwd"}</label></dt>
				<dd><input type="password" name="form[pwd]" id="pwd" value=""></dd>
				<dt><label for="pwd-repeat">{lang t="users|pwd_repeat"}</label></dt>
				<dd><input type="password" name="form[pwd_repeat]" id="pwd-repeat" value=""></dd>
			</dl>
		</div>
	</div>
{$captcha}
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>