{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="language-override-1" class="control-label">{lang t="users|allow_language_override"}</label>
		<div class="controls">
{foreach $languages as $row}
			<label for="language-override-{$row.value}" class="radio inline">
				<input type="radio" name="language_override" id="language-override-{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
	<div class="control-group">
		<label for="entries-override-1" class="control-label">{lang t="users|allow_entries_override"}</label>
		<div class="controls">
{foreach $entries as $row}
			<label for="entries-override-{$row.value}" class="radio inline">
				<input type="radio" name="entries_override" id="entries-override-{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
	<div class="control-group">
		<label for="enable-registration-1" class="control-label">{lang t="users|enable_registration"}</label>
		<div class="controls">
{foreach $registration as $row}
			<label for="enable-registration-{$row.value}" class="radio inline">
				<input type="radio" name="enable_registration" id="enable-registration-{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>