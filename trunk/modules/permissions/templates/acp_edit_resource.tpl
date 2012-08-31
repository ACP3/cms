{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="resource" class="control-label">{lang t="permissions|resource_name"}</label>
		<div class="controls"><input type="text" name="resource" id="resource" value="{$form.resource}" required></div>
	</div>
	<div class="control-group">
		<label for="privileges" class="control-label">{lang t="permissions|assigned_privilege"}</label>
		<div class="controls">
			<select name="privileges" id="privileges">
{foreach $privileges as $row}
				<option value="{$row.id}"{$row.selected}>{$row.key}{if !empty($row.description)} ({$row.description}){/if}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="form-actions">
		<input type="hidden" name="modules" value="{$form.modules}">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/permissions/list_resources"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>