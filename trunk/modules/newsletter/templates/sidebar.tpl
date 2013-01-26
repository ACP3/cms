<ul class="nav nav-list">
	<li class="nav-header">{lang t="newsletter|subscribe_newsletter"}</li>
	<li>
		<form action="{uri args="newsletter"}" method="post" accept-charset="UTF-8">
			<input type="email" name="mail" id="mail" maxlength="120" placeholder="{lang t="system|email_address"}" required style="width:auto">
{if isset($captcha)}
{$captcha}
{/if}
			<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
			<input type="hidden" name="action" value="subscribe">
			{$form_token}
		</form>
	</li>
</ul>