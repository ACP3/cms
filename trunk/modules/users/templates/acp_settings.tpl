{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="language-override-1" class="control-label">{lang t="users|allow_language_override"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $languages as $row}
				<input type="radio" name="language_override" id="language-override-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="language-override-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="entries-override-1" class="control-label">{lang t="users|allow_entries_override"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $entries as $row}
				<input type="radio" name="entries_override" id="entries-override-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="entries-override-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="enable-registration-1" class="control-label">{lang t="users|enable_registration"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $registration as $row}
				<input type="radio" name="enable_registration" id="enable-registration-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="enable-registration-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/users"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>