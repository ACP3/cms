{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="users|contact"}</a></li>
			<li><a href="#tab-3">{lang t="users|pwd"}</a></li>
		</ul>
		<div id="tab-1">
			<p>
				{lang t="users|display_profile_field"}
			</p>
			<table class="acp-table no-border">
				<tr>
					<td style="width:5%"></td>
					<td>
						<dl>
							<dt><label for="nickname">{lang t="users|nickname"}</label></dt>
							<dd><input type="text" name="form[nickname]" id="nickname" value="{$form.nickname}" maxlength="30"></dd>
						</dl>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="form[realname_display]" id="realname_display" value="1" class="checkbox"{$checked.realname}>
					</td>
					<td>
						<dl>
							<dt><label for="realname">{lang t="users|realname"}</label></dt>
							<dd><input type="text" name="form[realname]" id="realname" value="{$form.realname}" maxlength="80"></dd>
						</dl>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="form[gender_display]" id="gender_display" value="1" class="checkbox"{$checked.gender}>
					</td>
					<td>
						<dl>
							<dt><label for="gender">{lang t="users|gender"}</label></dt>
							<dd>
								<select name="form[gender]" id="gender">
{foreach $gender as $row}
									<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
								</select>
							</dd>
						</dl>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="form[birthday_display]" id="birthday_display" value="1" class="checkbox"{$checked.birthday}>
					</td>
					<td>
						<dl>
							<dt><label for="birthday">{lang t="users|birthday"}</label></dt>
							<dd>
								{$birthday_datepicker}
								<div>
{foreach $birthday_format as $row}
									<label for="{$row.name}">
										<input type="radio" name="form[birthday_format]" id="{$row.name}" value="{$row.value}" class="checkbox"{$row.checked}>
										{$row.lang}
									</label>
{/foreach}
								</div>
							</dd>
						</dl>
					</td>
				</tr>
			</table>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<p>
				{lang t="users|display_profile_field"}
			</p>
			<table class="acp-table no-border">
{foreach $contact as $row}
				<tr>
					<td style="width:5%">
						<input type="checkbox" name="form[{$row.name}_display]" id="{$row.name}_display" value="1" class="checkbox"{$row.checked}>
					</td>
					<td>
						<dl>
							<dt><label for="{$row.name}">{$row.lang}</label></dt>
							<dd><input type="text" name="form[{$row.name}]" id="{$row.name}" value="{$row.value}" maxlength="{$row.maxlength}"></dd>
						</dl>
					</td>
				</tr>
{/foreach}
			</table>
		</div>
		<div id="tab-3" class="ui-tabs-hide">
			<dl>
				<dt><label for="new_pwd">{lang t="users|new_pwd"}</label></dt>
				<dd><input type="password" name="form[new_pwd]" id="new_pwd"></dd>
				<dt><label for="new_pwd_repeat">{lang t="users|new_pwd_repeat"}</label></dt>
				<dd><input type="password" name="form[new_pwd_repeat]" id="new_pwd_repeat"></dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>