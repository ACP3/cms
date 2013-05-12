{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="system|email_address"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="{$languages.0.id}" class="control-label">{lang t="users|allow_language_override"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $languages as $row}
				<input type="radio" name="language_override" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="{$entries.0.id}" class="control-label">{lang t="users|allow_entries_override"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $entries as $row}
				<input type="radio" name="entries_override" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="{$registration.0.id}" class="control-label">{lang t="users|enable_registration"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $registration as $row}
				<input type="radio" name="enable_registration" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/users"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>