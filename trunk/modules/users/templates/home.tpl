<div id="adm-list" class="well">
	{check_access mode="link" path="users/edit_profile" icon="32/edit_user" width="32" height="32"}
	{check_access mode="link" path="users/edit_settings" icon="32/advancedsettings" width="32" height="32"}
</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="users|drafts"}</legend>
		{wysiwyg name="draft" value="$draft" height="250" toolbar="simple"}
	</fieldset>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
	</div>
</form>