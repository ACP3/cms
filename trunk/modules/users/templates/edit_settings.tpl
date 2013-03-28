{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="system|date"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="users|privacy"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="language" class="control-label">{lang t="users|language"}</label>
					<div class="controls">
						<select name="language" id="language"{if $language_override == 0} disabled{/if}>
							<option value="">{lang t="system|pls_select"}</option>
{foreach $languages as $row}
							<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="entries" class="control-label">{lang t="system|records_per_page"}</label>
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
					<label for="date-format-long" class="control-label">{lang t="system|date_format_long"}</label>
					<div class="controls">
						<input type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20">
						<p class="help-block">{lang t="system|php_date_function"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="date-format-short" class="control-label">{lang t="system|date_format_short"}</label>
					<div class="controls"><input type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></div>
				</div>
				<div class="control-group">
					<label for="time-zone" class="control-label">{lang t="system|time_zone"}</label>
					<div class="controls">
						<select name="date_time_zone" id="date-time-zone">
{foreach $time_zones as $key => $values}
							<optgroup label="{$key}">
{foreach $values as $country => $value}
								<option value="{$country}"{$value.selected}>{$country}</option>
{/foreach}
							</optgroup>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
				<div class="control-group">
					<label for="mail-display-{$mail_display.0.value}" class="control-label">{lang t="users|display_mail"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $mail_display as $row}
							<input type="radio" name="mail_display" id="mail-display-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="mail-display-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="address-display-{$address_display.0.value}" class="control-label">{lang t="users|display_address"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $address_display as $row}
							<input type="radio" name="address_display" id="address-display-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="address-display-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="country-display-{$country_display.0.value}" class="control-label">{lang t="users|display_country"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $country_display as $row}
							<input type="radio" name="country_display" id="country-display-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="country-display-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="birthday-display-{$birthday_display.0.value}" class="control-label">{lang t="users|birthday"}</label>
					<div class="controls">
{foreach $birthday_display as $row}
						<label for="birthday-display-{$row.value}" class="radio">
							<input type="radio" name="birthday_display" id="birthday-display-{$row.value}" value="{$row.value}"{$row.checked}>
							{$row.lang}
						</label>
{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="users/home"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>