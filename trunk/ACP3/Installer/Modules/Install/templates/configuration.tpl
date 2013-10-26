{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="install/configuration"}" method="post" accept-charset="UTF-8" class="form-horizontal" id="config-form">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tabs-1" data-toggle="tab">{lang t="db_connection_settings"}</a></li>
			<li><a href="#tabs-2" data-toggle="tab">{lang t="admin_account"}</a></li>
			<li><a href="#tabs-3" data-toggle="tab">{lang t="general"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tabs-1" class="tab-pane active">
				<div class="form-group">
					<label for="db-host" class="col-lg-2 control-label">{lang t="db_hostname"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="db_host" id="db-host" value="{$form.db_host}" required>
						<p class="help-block">{lang t="db_hostname_description"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="db-user" class="col-lg-2 control-label">{lang t="db_username"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="db_user" id="db-user" value="{$form.db_user}" required></div>
				</div>
				<div class="form-group">
					<label for="db-password" class="col-lg-2 control-label">{lang t="db_password"}</label>
					<div class="col-lg-10"><input class="form-control" type="password" name="db_password" id="db-password" value=""></div>
				</div>
				<div class="form-group">
					<label for="db-name" class="col-lg-2 control-label">{lang t="db_name"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="db_name" id="db-name" value="{$form.db_name}" required></div>
				</div>
				<div class="form-group">
					<label for="db-pre" class="col-lg-2 control-label">{lang t="db_table_prefix"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="db_pre" id="db-pre" value="{$form.db_pre}"></div>
				</div>
			</div>
			<div id="tabs-2" class="tab-pane">
				<div class="form-group">
					<label for="user-name" class="col-lg-2 control-label">{lang t="nickname"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="user_name" id="user-name" value="{$form.user_name}" required></div>
				</div>
				<div class="form-group">
					<label for="user-pwd" class="col-lg-2 control-label">{lang t="pwd"}</label>
					<div class="col-lg-10"><input class="form-control" type="password" name="user_pwd" id="user-pwd" required></div>
				</div>
				<div class="form-group">
					<label for="user-pwd-wdh" class="col-lg-2 control-label">{lang t="pwd_repeat"}</label>
					<div class="col-lg-10"><input class="form-control" type="password" name="user_pwd_wdh" id="user-pwd-wdh" required></div>
				</div>
				<div class="form-group">
					<label for="mail" class="col-lg-2 control-label">{lang t="email"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="mail" id="mail" value="{$form.mail}" required></div>
				</div>
			</div>
			<div id="tabs-3" class="tab-pane">
				<div class="form-group">
					<label for="seo-title" class="col-lg-2 control-label">{lang t="site-title"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="seo_title" id="seo-title" value="{$form.seo_title}" required></div>
				</div>
				<div class="form-group">
					<label for="date-format-long" class="col-lg-2 control-label">{lang t="date_format_long"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20" required>
						<p class="help-block">{lang t="php_date_function"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="date-format-short" class="col-lg-2 control-label">{lang t="date_format_short"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20" required></div>
				</div>
				<div class="form-group">
					<label for="date-time-zone" class="col-lg-2 control-label">{lang t="time_zone"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="date_time_zone" id="date-time-zone">
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
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="submit"}</button>
		</div>
	</div>
</form>