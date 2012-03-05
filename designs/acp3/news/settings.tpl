{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}news/settings.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="news|settings"}</legend>
		<dl>
			<dt><label for="date-format">{lang t="common|date_format"}</label></dt>
			<dd>
				<select name="dateformat" id="date-format">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="sidebar-entries">{lang t="common|sidebar_entries_to_display"}</label></dt>
			<dd>
				<select name="sidebar" id="sidebar-entries">
					<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
					<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="readmore-1">{lang t="news|activate_readmore"}</label></dt>
			<dd>
{foreach $readmore as $row}
				<label for="readmore-{$row.value}">
					<input type="radio" name="readmore" id="readmore-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
		<dl id="readmore-container">
			<dt><label for="readmore-chars">{lang t="news|readmore_chars"}</label></dt>
			<dd><input type="number" name="readmore_chars" id="readmore-chars" value="{$readmore_chars}"></dd>
		</dl>
		<dl>
			<dt><label for="category-in-breadcrumb-1">{lang t="news|display_category_in_breadcrumb"}</label></dt>
			<dd>
{foreach $category_in_breadcrumb as $row}
				<label for="category-in-breadcrumb-{$row.value}">
					<input type="radio" name="category_in_breadcrumb" id="category-in-breadcrumb-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{if isset($allow_comments)}
			<dt><label for="comments-1">{lang t="common|allow_comments"}</label></dt>
			<dd>
{foreach $allow_comments as $row}
				<label for="comments-{$row.value}">
					<input type="radio" name="comments" id="comments-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{/if}
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>