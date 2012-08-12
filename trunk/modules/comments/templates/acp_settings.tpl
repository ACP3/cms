{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="comments|acp_settings"}</legend>
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
{foreach $allow_emoticons as $row}
				<label for="emoticons-{$row.value}" class="checkbox">
					<input type="radio" name="emoticons" id="emoticons-{$row.value}" value="{$row.value}"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</div>
		</div>
{/if}
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>