{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="system|general"}</a></li>
			<li><a href="#tab-2">{lang t="common|date"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="language">{lang t="users|language"}</label></dt>
				<dd>
					<select name="language" id="language"{if $language_override == 0} disabled{/if}>
						<option value="">{lang t="common|pls_select"}</option>
{foreach $languages as $row}
						<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="entries">{lang t="common|records_per_page"}</label></dt>
				<dd>
					<select name="entries" id="entries"{if $entries_override == 0} disabled{/if}>
{foreach $entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-2">
			<dl>
				<dt>
					<label for="date-format-long">{lang t="common|date_format_long"}</label>
					<span>({lang t="system|php_date_function"})</span>
				</dt>
				<dd><input type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20"></dd>
				<dt><label for="date-format-short">{lang t="common|date_format_short"}</label></dt>
				<dd><input type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></dd>
				<dt><label for="time-zone">{lang t="common|time_zone"}</label></dt>
				<dd>
					<select name="time_zone" id="time-zone">
{foreach $time_zone as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="dst-1">{lang t="common|daylight_saving_time"}</label></dt>
				<dd>
{foreach $dst as $row}
					<label for="dst-{$row.value}">
						<input type="radio" name="dst" id="dst-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>