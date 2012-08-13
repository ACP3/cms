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
	<div class="control-group">
		<label for="sidebar-entries" class="control-label">{lang t="common|sidebar_entries_to_display"}</label>
		<div class="controls">
			<select name="sidebar" id="sidebar-entries">
				<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
				<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
			</select>
		</div>
	</div>
{if isset($comments)}
	<div class="control-group">
		<label for="comments-1" class="control-label">{lang t="common|allow_comments"}</label>
		<div class="controls">
{foreach $comments as $row}
			<label for="comments-{$row.value}" class="radio inline">
				<input type="radio" name="comments" id="comments-{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
{/if}
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>