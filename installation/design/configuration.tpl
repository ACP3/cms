{if isset($sql_queries)}
<p style="text-align:center">
	<a href="#" id="queries-link">{lang t="installation|show_hide_executed_db_queries"}</a>
</p>
<table id="queries" class="table table-condensed">
	<thead>
		<tr>
			<th>{lang t="system|sql_query"}</th>
			<th style="width:10%">{lang t="system|result"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $sql_queries as $row}
		<tr>
			<td>{$row.query}</td>
			<td class="alert {$row.class}">{$row.result}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{if isset($install_error)}
<p>
	{lang t="installation|installation_error"}
</p>
<div class="form-actions" style="text-align:center">
	<a href="{uri args="overview/welcome"}" class="btn">{lang t="common|back"}</a>
</div>
{else}
<p>
	{lang t="installation|installation_successful_1"}
</p>
<p>
	{lang t="installation|installation_successful_2"}
</p>
<div class="form-actions" style="text-align:center">
	<a href="{$INSTALLER_DIR}../" class="btn">{lang t="common|forward"}</a>
</div>
{/if}
{else}
{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="install/configuration"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tabs-1" data-toggle="tab">{lang t="installation|db_connection_settings"}</a></li>
			<li><a href="#tabs-2" data-toggle="tab">{lang t="installation|admin_account"}</a></li>
			<li><a href="#tabs-3" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tabs-4" data-toggle="tab">{lang t="common|date"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tabs-1" class="tab-pane active">
				<div class="control-group">
					<label for="db-host" class="control-label">{lang t="installation|db_hostname"}</label>
					<div class="controls">
						<input type="text" name="db_host" id="db-host" value="{$form.db_host}">
						<p class="help-block">{lang t="installation|db_hostname_description"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="db-user" class="control-label">{lang t="installation|db_username"}</label>
					<div class="controls"><input type="text" name="db_user" id="db-user" value="{$form.db_user}"></div>
				</div>
				<div class="control-group">
					<label for="db-password" class="control-label">{lang t="installation|db_password"}</label>
					<div class="controls"><input type="password" name="db_password" id="db-password" value=""></div>
				</div>
				<div class="control-group">
					<label for="db-name" class="control-label">{lang t="installation|db_name"}</label>
					<div class="controls"><input type="text" name="db_name" id="db-name" value="{$form.db_name}"></div>
				</div>
				<div class="control-group">
					<label for="db-pre" class="control-label">{lang t="installation|db_table_prefix"}</label>
					<div class="controls"><input type="text" name="db_pre" id="db-pre" value="{$form.db_pre}"></div>
				</div>
			</div>
			<div id="tabs-2" class="tab-pane">
				<div class="control-group">
					<label for="user-name" class="control-label">{lang t="users|nickname"}</label>
					<div class="controls"><input type="text" name="user_name" id="user-name" value="{$form.user_name}"></div>
				</div>
				<div class="control-group">
					<label for="user-pwd" class="control-label">{lang t="users|pwd"}</label>
					<div class="controls"><input type="password" name="user_pwd" id="user-pwd"></div>
				</div>
				<div class="control-group">
					<label for="user-pwd-wdh" class="control-label">{lang t="users|pwd_repeat"}</label>
					<div class="controls"><input type="password" name="user_pwd_wdh" id="user-pwd-wdh"></div>
				</div>
				<div class="control-group">
					<label for="mail" class="control-label">{lang t="common|email"}</label>
					<div class="controls"><input type="text" name="mail" id="mail" value="{$form.mail}"></div>
				</div>
			</div>
			<div id="tabs-3" class="tab-pane">
				<div class="control-group">
					<label for="seo-title" class="control-label">{lang t="system|title"}</label>
					<div class="controls"><input type="text" name="seo_title" id="seo-title" value="{$form.seo_title}"></div>
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
				<div class="control-group">
					<label for="flood" class="control-label">{lang t="system|flood_barrier"}</label>
					<div class="controls">
						<input type="text" name="flood" id="flood" value="{$form.flood}" maxlength="3">
						<p class="help-block">{lang t="system|flood_barrier_description"}</p>
					</div>
				</div>
			</div>
			<div id="tabs-4" class="tab-pane">
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
					<label for="date-time-zone" class="control-label">{lang t="common|time_zone"}</label>
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
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
	</div>
</form>
{/if}