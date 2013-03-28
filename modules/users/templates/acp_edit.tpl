{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="users|contact"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="users|address"}</a></li>
			<li><a href="#tab-4" data-toggle="tab">{lang t="users|acp_settings"}</a></li>
			<li><a href="#tab-5" data-toggle="tab">{lang t="users|privacy"}</a></li>
			<li><a href="#tab-6" data-toggle="tab">{lang t="users|pwd"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="nickname" class="control-label">{lang t="users|nickname"}</label>
					<div class="controls"><input type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30"></div>
				</div>
				<div class="control-group">
					<label for="realname" class="control-label">{lang t="users|realname"}</label>
					<div class="controls"><input type="text" name="realname" id="realname" value="{$form.realname}" maxlength="80"></div>
				</div>
				<div class="control-group">
					<label for="gender" class="control-label">{lang t="users|gender"}</label>
					<div class="controls">
						<select name="gender" id="gender">
{foreach $gender as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="birthday" class="control-label">{lang t="users|birthday"}</label>
					<div class="controls">
						{$birthday_datepicker}
					</div>
				</div>
				<div class="control-group">
					<label for="roles" class="control-label">{lang t="permissions|roles"}</label>
					<div class="controls">
						<select name="roles[]" id="roles" multiple="multiple" style="height:100px">
{foreach $roles as $row}
							<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="super-user-1" class="control-label">{lang t="users|super_user"}</label>
						<div class="controls">
							<div class="btn-group" data-toggle="radio">
{foreach $super_user as $row}
								<input type="radio" name="super_user" id="super-user-{$row.value}" value="{$row.value}"{$row.checked}>
								<label for="super-user-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
{foreach $contact as $row}
				<div class="control-group">
					<label for="{$row.name}" class="control-label">{$row.lang}</label>
					<div class="controls"><input type="text" name="{$row.name}" id="{$row.name}" value="{$row.value}" maxlength="{$row.maxlength}"></div>
				</div>
{/foreach}
			</div>
			<div id="tab-3" class="tab-pane">
				<div class="control-group">
					<label for="street" class="control-label">{lang t="users|address_street"}</label>
					<div class="controls"><input type="text" name="street" id="street" value="{$form.street}" maxlength="80"></div>
				</div>
				<div class="control-group">
					<label for="house-number" class="control-label">{lang t="users|address_house_number"}</label>
					<div class="controls"><input type="text" name="house_number" id="house-number" value="{$form.house_number}" maxlength="5"></div>
				</div>
				<div class="control-group">
					<label for="zip" class="control-label">{lang t="users|address_zip"}</label>
					<div class="controls"><input type="text" name="zip" id="zip" value="{$form.zip}" maxlength="5"></div>
				</div>
				<div class="control-group">
					<label for="city" class="control-label">{lang t="users|address_city"}</label>
					<div class="controls"><input type="text" name="city" id="city" value="{$form.city}" maxlength="80"></div>
				</div>
				<div class="control-group">
					<label for="country" class="control-label">{lang t="users|country"}</label>
					<div class="controls">
						<select name="country" id="country">
							<option value="">{lang t="system|pls_select"}</option>
{foreach $countries as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-4" class="tab-pane">
				<div class="control-group">
					<label for="language" class="control-label">{lang t="users|language"}</label>
					<div class="controls">
						<select name="language" id="language">
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
						<select name="entries" id="entries">
{foreach $entries as $row}
							<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
						</select>
					</div>
				</div>
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
								<option value="{$country}" style="margin:0 0 0 10px"{$value.selected}>{$country}</option>
{/foreach}
							</optgroup>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-5" class="tab-pane">
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
			<div id="tab-6" class="tab-pane">
				<div class="control-group">
					<label for="new-pwd" class="control-label">{lang t="users|new_pwd"}</label>
					<div class="controls"><input type="password" name="new_pwd" id="new-pwd"></div>
				</div>
				<div class="control-group">
					<label for="new_pwd_repeat" class="control-label">{lang t="users|new_pwd_repeat"}</label>
					<div class="controls"><input type="password" name="new_pwd_repeat" id="new_pwd_repeat"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/users"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>