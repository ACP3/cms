{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="users|acp_settings"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="common|date"}</a></li>
			<li><a href="#tab-4" data-toggle="tab">{lang t="users|pwd"}</a></li>
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
					<label for="mail" class="control-label">{lang t="common|email"}</label>
					<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="website" class="control-label">{lang t="common|website"}</label>
					<div class="controls"><input type="url" name="website" id="website" value="{$form.website}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="roles" class="control-label">{lang t="access|roles"}</label>
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
				<div class="control-group">
					<label for="language" class="control-label">{lang t="users|language"}</label>
					<div class="controls">
						<select name="language" id="language">
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
						<select name="entries" id="entries">
{foreach $entries as $row}
							<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
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
			<div id="tab-4" class="tab-pane">
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
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/users"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>