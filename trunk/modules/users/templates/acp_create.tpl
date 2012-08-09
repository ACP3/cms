{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="system|general"}</a></li>
			<li><a href="#tab-2">{lang t="users|settings"}</a></li>
			<li><a href="#tab-3">{lang t="common|date"}</a></li>
			<li><a href="#tab-4">{lang t="users|pwd"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="nickname">{lang t="users|nickname"}</label></dt>
				<dd><input type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30"></dd>
			</dl>
			<dl>
				<dt><label for="realname">{lang t="users|realname"}</label></dt>
				<dd><input type="text" name="realname" id="realname" value="{$form.realname}" maxlength="80"></dd>
			</dl>
			<dl>
				<dt><label for="mail">{lang t="common|email"}</label></dt>
				<dd><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></dd>
			</dl>
			<dl>
				<dt><label for="website">{lang t="common|website"}</label></dt>
				<dd><input type="url" name="website" id="website" value="{$form.website}" maxlength="120"></dd>
			</dl>
			<dl>
				<dt><label for="roles">{lang t="access|roles"}</label></dt>
				<dd>
					<select name="roles[]" id="roles" multiple="multiple" style="height:100px">
{foreach $roles as $row}
						<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
				<dt><label for="super-user-1">{lang t="users|super_user"}</label></dt>
				<dd>
{foreach $super_user as $row}
					<label for="super-user-{$row.value}">
						<input type="radio" name="super_user" id="super-user-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
		<div id="tab-2">
			<dl>
				<dt><label for="language">{lang t="users|language"}</label></dt>
				<dd>
					<select name="language" id="language">
						<option value="">{lang t="common|pls_select"}</option>
{foreach $languages as $row}
						<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
				<dt><label for="entries">{lang t="common|records_per_page"}</label></dt>
				<dd>
					<select name="entries" id="entries">
{foreach $entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-3">
			<dl>
				<dt>
					<label for="date-format-long">{lang t="common|date_format_long"}</label>
					<span>({lang t="system|php_date_function"})</span>
				</dt>
				<dd><input type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20"></dd>
			</dl>
			<dl>
				<dt><label for="date-format-short">{lang t="common|date_format_short"}</label></dt>
				<dd><input type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></dd>
			</dl>
			<dl>
				<dt><label for="time-zone">{lang t="common|time_zone"}</label></dt>
				<dd>
					<select name="date_time_zone" id="date-time-zone">
{foreach $time_zones as $key => $values}
						<optgroup label="{$key}">
{foreach $values as $country => $value}
							<option value="{$country}" style="margin:0 0 0 10px"{$value.selected}>{$country}</option>
{/foreach}
						</optgroup>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-4">
			<dl>
				<dt><label for="pwd">{lang t="users|pwd"}</label></dt>
				<dd><input type="password" name="pwd" id="pwd"></dd>
			</dl>
			<dl>
				<dt><label for="pwd-repeat">{lang t="users|pwd_repeat"}</label></dt>
				<dd><input type="password" name="pwd_repeat" id="pwd-repeat"></dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>