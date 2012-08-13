{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="privileges" class="control-label">{lang t="access|assigned_privilege"}</label>
		<div class="controls">
			<select name="privileges" id="privileges">
{foreach $privileges as $row}
				<option value="{$row.id}"{$row.selected}>{$row.key}{if !empty($row.description)} ({$row.description}){/if}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>