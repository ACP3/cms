{if isset($sql_queries)}
<div style="text-align:center">
	<a href="#" id="queries-link">{lang t="installation|show_hide_executed_db_queries"}</a>
</div>
<table id="queries" class="acp-table">
	<thead>
		<tr>
			<th>{lang t="system|sql_query"}</th>
			<th style="width:10%">{lang t="system|result"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $sql_queries as $row}
		<tr>
			<td style="text-align:left">{$row.query}</td>
			<td style="color:#fff;background:#{$row.color}">{$row.result}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{if isset($install_error)}
<p>
	{lang t="installation|installation_error"}
</p>
<div class="success forward">
	<a href="{uri args="overview/welcome"}" class="form">{lang t="common|back"}</a>
</div>
{else}
<p>
	{lang t="installation|installation_successful_1"}
</p>
<p>
	{lang t="installation|installation_successful_2"}
</p>
<div class="success forward">
	<a href="{$ROOT_DIR}../" class="form">{lang t="common|forward"}</a>
</div>
{/if}
{else}
{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="install/configuration"}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">{lang t="installation|db_connection_settings"}</a></li>
			<li><a href="#tabs-2">{lang t="installation|admin_account"}</a></li>
			<li><a href="#tabs-3">{lang t="system|general"}</a></li>
			<li><a href="#tabs-4">{lang t="common|date"}</a></li>
		</ul>
		<div id="tabs-1">
			<dl>
				<dt>
					<label for="db-host">{lang t="installation|db_hostname"}</label>
					<span>{lang t="installation|db_hostname_description"}</span>
				</dt>
				<dd><input type="text" name="db_host" id="db-host" value="{$form.db_host}"></dd>
			</dl>
			<dl>
				<dt><label for="db-user">{lang t="installation|db_username"}</label></dt>
				<dd><input type="text" name="db_user" id="db-user" value="{$form.db_user}"></dd>
			</dl>
			<dl>
				<dt><label for="db-password">{lang t="installation|db_password"}</label></dt>
				<dd><input type="password" name="db_password" id="db-password" value=""></dd>
			</dl>
			<dl>
				<dt><label for="db-name">{lang t="installation|db_name"}</label></dt>
				<dd><input type="text" name="db_name" id="db-name" value="{$form.db_name}"></dd>
			</dl>
			<dl>
				<dt><label for="db-pre">{lang t="installation|db_table_prefix"}</label></dt>
				<dd><input type="text" name="db_pre" id="db-pre" value="{$form.db_pre}"></dd>
			</dl>
		</div>
		<div id="tabs-2" class="ui-tabs-hide">
			<dl>
				<dt><label for="user-name">{lang t="users|nickname"}</label></dt>
				<dd><input type="text" name="user_name" id="user-name" value="{$form.user_name}"></dd>
			</dl>
			<dl>
				<dt><label for="user-pwd">{lang t="users|pwd"}</label></dt>
				<dd><input type="password" name="user_pwd" id="user-pwd"></dd>
			</dl>
			<dl>
				<dt><label for="user-pwd-wdh">{lang t="users|pwd_repeat"}</label></dt>
				<dd><input type="password" name="user_pwd_wdh" id="user-pwd-wdh"></dd>
			</dl>
			<dl>
				<dt><label for="mail">{lang t="common|email"}</label></dt>
				<dd><input type="text" name="mail" id="mail" value="{$form.mail}"></dd>
			</dl>
		</div>
		<div id="tabs-3" class="ui-tabs-hide">
			<dl>
				<dt><label for="seo-title">{lang t="system|title"}</label></dt>
				<dd><input type="text" name="seo_title" id="seo-title" value="{$form.seo_title}"></dd>
			</dl>
			<dl>
				<dt><label for="entries">{lang t="system|entries_per_page"}</label></dt>
				<dd>
					<select name="entries" id="entries">
{foreach $entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
				<dt>
					<label for="flood">{lang t="system|flood_barrier"}</label>
					<span>({lang t="system|flood_barrier_description"})</span>
				</dt>
				<dd><input type="text" name="flood" id="flood" value="{$form.flood}" maxlength="3"></dd>
			</dl>
		</div>
		<div id="tabs-4" class="ui-tabs-hide">
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
				<dt><label for="date-time-zone">{lang t="common|time_zone"}</label></dt>
				<dd>
					<select name="date_time_zone" id="date-time-zone">
{foreach $time_zones as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
				<dt><label for="date-dst-1">{lang t="common|daylight_saving_time"}</label></dt>
				<dd>
{foreach $dst as $row}
					<label for="date-dst-{$row.value}">
						<input type="radio" name="date_dst" id="date-dst-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
	</div>
	<br>
	<div class="success forward">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
	</div>
</form>
{/if}