{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="common|date"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="language" class="control-label">{lang t="users|language"}</label>
					<div class="controls">
						<select name="language" id="language"{if $language_override == 0} disabled{/if}>
							<option value="">{lang t="common|pls_select"}</option>
{foreach $languages as $row}
							<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="entries" class="control-label">{lang t="common|records_per_page"}</label>
					<div class="controls">
						<select name="entries" id="entries"{if $entries_override == 0} disabled{/if}>
{foreach $entries as $row}
							<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="date-format-long" class="control-label">{lang t="common|date_format_long"}</label>
					<div class="controls">
						<input type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20">
						<p class="help-block">{lang t="system|php_date_function"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="date-format-short" class="control-label">{lang t="common|date_format_short"}</label>
					<div class="controls"><input type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></div>
				</div>
				<div class="control-group">
					<label for="time-zone" class="control-label">{lang t="common|time_zone"}</label>
					<div class="controls">
						<select name="date_time_zone" id="date-time-zone">
{foreach $time_zones as $key => $values}
							<optgroup label="{$key}">
{foreach $values as $country => $value}
								<option value="{$country}" style="margin:0 0 0 10px"{$value.selected}>{$country}</option>
{/foreach}
							</optgroup>
{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>