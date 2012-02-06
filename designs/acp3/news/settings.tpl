{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="news|settings"}</legend>
		<dl>
			<dt><label for="readmore-1">{lang t="news|activate_readmore"}</label></dt>
			<dd>
{foreach $readmore as $row}
				<label for="readmore-{$row.value}">
					<input type="radio" name="form[readmore]" id="readmore-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
			<dt><label for="readmore_chars">{lang t="news|readmore_chars"}</label></dt>
			<dd><input type="number" name="form[readmore_chars]" id="readmore_chars" value="{$readmore_chars}"></dd>
{if isset($allow_comments)}
			<dt><label for="comments-1">{lang t="common|allow_comments"}</label></dt>
			<dd>
{foreach $allow_comments as $row}
				<label for="comments-{$row.value}">
					<input type="radio" name="form[comments]" id="comments-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{/if}
			<dt><label for="date-format">{lang t="common|date_format"}</label></dt>
			<dd>
				<select name="form[dateformat]" id="date-format">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="sidebar-entries">{lang t="common|sidebar_entries_to_display"}</label></dt>
			<dd>
				<select name="form[sidebar]" id="sidebar-entries">
					<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
					<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
				</select>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>