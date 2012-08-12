{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="users|nickname"} &amp; {lang t="common|email"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="users|pwd"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="nickname" class="control-label">{lang t="users|nickname"}</label>
					<div class="controls"><input type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30"></div>
				</div>
				<div class="control-group">
					<label for="mail" class="control-label">{lang t="common|email"}</label>
					<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="pwd" class="control-label">{lang t="users|pwd"}</label>
					<div class="controls"><input type="password" name="pwd" id="pwd" value=""></div>
				</div>
				<div class="control-group">
					<label for="pwd-repeat" class="control-label">{lang t="users|pwd_repeat"}</label>
					<div class="controls"><input type="password" name="pwd_repeat" id="pwd-repeat" value=""></div>
				</div>
			</div>
		</div>
	</div>
{$captcha}
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>