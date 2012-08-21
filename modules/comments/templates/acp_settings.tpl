{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="date-format" class="control-label">{lang t="common|date_format"}</label>
		<div class="controls">
			<select name="dateformat" id="date-format">
				<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
				<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
			</select>
		</div>
	</div>
{if isset($allow_emoticons)}
	<div class="control-group">
		<label for="emoticons-1" class="control-label">{lang t="comments|allow_emoticons"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $allow_emoticons as $row}
				<input type="radio" name="emoticons" id="emoticons-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="emoticons-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/comments"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>