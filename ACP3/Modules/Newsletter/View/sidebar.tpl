<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="newsletter|subscribe_newsletter"}</h3>
	</div>
	<div class="panel-body">
		<form action="{uri args="newsletter"}" method="post" accept-charset="UTF-8">
			<div class="form-group">
				<input class="form-control" type="email" name="mail" maxlength="120" placeholder="{lang t="system|email_address"}" required>
			</div>
			{if isset($captcha)}
				{$captcha}
			{/if}
			<div class="form-group" style="margin: 10px 0 0">
				<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
				<input type="hidden" name="action" value="subscribe">
				{$form_token}
			</div>
		</form>
	</div>
</div>