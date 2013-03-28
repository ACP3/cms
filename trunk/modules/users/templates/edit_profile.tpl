{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="users|contact"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="users|address"}</a></li>
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
		<a href="{uri args="users/home"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>