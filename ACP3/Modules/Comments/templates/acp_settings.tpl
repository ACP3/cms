{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="date-format" class="control-label">{lang t="system|date_format"}</label>
		<div class="controls">
			<select name="dateformat" id="date-format">
{foreach $dateformat as $row}
				<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
			</select>
		</div>
	</div>
{if isset($allow_emoticons)}
	<div class="control-group">
		<label for="{$allow_emoticons.0.id}" class="control-label">{lang t="comments|allow_emoticons"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $allow_emoticons as $row}
				<input type="radio" name="emoticons" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/comments"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>