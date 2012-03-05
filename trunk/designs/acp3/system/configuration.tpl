{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}system/configuration.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="system|general"}</a></li>
			<li><a href="#tab-2">{lang t="common|date"}</a></li>
			<li><a href="#tab-3">{lang t="system|maintenance"}</a></li>
			<li><a href="#tab-4">{lang t="common|seo"}</a></li>
			<li><a href="#tab-5">{lang t="system|performance"}</a></li>
			<li><a href="#tab-6">{lang t="system|email"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="entries">{lang t="common|records_per_page"}</label></dt>
				<dd>
					<select name="entries" id="entries">
{foreach $entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
				<dt>
					<label for="flood">{lang t="system|flood_barrier"}</label>
					<span>({lang t="system|flood_barrier_description"})</span>
				</dt>
				<dd><input type="number" name="flood" id="flood" value="{$form.flood}"></dd>
				<dt>
					<label for="homepage">{lang t="system|homepage"}</label>
					<span>({lang t="system|homepage_description"})</span>
				</dt>
				<dd><input type="text" name="homepage" id="homepage" value="{$form.homepage}"></dd>
				<dt><label for="wysiwyg">{lang t="system|editor"}</label></dt>
				<dd>
					<select name="wysiwyg" id="wysiwyg">
{foreach $wysiwyg as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt>
					<label for="date-format-long">{lang t="common|date_format_long"}</label>
					<span>({lang t="system|php_date_function"})</span>
				</dt>
				<dd><input type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20"></dd>
				<dt><label for="date-format-short">{lang t="common|date_format_short"}</label></dt>
				<dd><input type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></dd>
				<dt><label for="date-time-zone">{lang t="common|time_zone"}</label></dt>
				<dd>
					<select name="date_time_zone" id="date-time-zone">
{foreach $time_zone as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
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
		<div id="tab-3" class="ui-tabs-hide">
			<dl>
				<dt><label for="maintenance-mode-1">{lang t="system|maintenance_mode"}</label></dt>
				<dd>
{foreach $maintenance as $row}
					<label for="maintenance-mode-{$row.value}">
						<input type="radio" name="maintenance_mode" id="maintenance-mode-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt><label for="maintenance-message">{lang t="system|maintenance_msg"}</label></dt>
				<dd><textarea name="maintenance_message" id="maintenance-message" cols="50" rows="6">{$form.maintenance_message}</textarea></dd>
			</dl>
		</div>
		<div id="tab-4" class="ui-tabs-hide">
			<dl>
				<dt><label for="seo-title">{lang t="system|title"}</label></dt>
				<dd><input type="text" name="seo_title" id="seo-title" value="{$form.seo_title}" maxlength="120"></dd>
				<dt><label for="seo-meta-description">{lang t="common|seo_description"}</label></dt>
				<dd><input type="text" name="seo_meta_description" id="seo-meta-description" value="{$form.seo_meta_description}" maxlength="120"></dd>
				<dt>
					<label for="seo-meta-keywords">{lang t="common|seo_keywords"}</label>
					<span>({lang t="common|seo_keywords_separate_with_commas"})</span>
				</dt>
				<dd><textarea name="seo_meta_keywords" id="seo-meta-keywords" cols="50" rows="6">{$form.seo_meta_keywords}</textarea></dd>
				<dt><label for="seo-robots">{lang t="common|seo_robots"}</label></dt>
				<dd>
					<select name="seo_robots" id="seo-robots">
{foreach $robots as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="seo-aliases-1">{lang t="system|enable_seo_aliases"}</label></dt>
				<dd>
{foreach $aliases as $row}
					<label for="seo-aliases-{$row.value}">
						<input type="radio" name="seo_aliases" id="seo-aliases-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt>
					<label for="seo-mod-rewrite-1">{lang t="system|mod_rewrite"}</label>
					<span>({lang t="system|mod_rewrite_description"})</span>
				</dt>
				<dd>
{foreach $mod_rewrite as $row}
					<label for="seo-mod-rewrite-{$row.value}">
						<input type="radio" name="seo_mod_rewrite" id="seo-mod-rewrite-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
		<div id="tab-5" class="ui-tabs-hide">
			<dl>
				<dt><label for="cache-images-1">{lang t="system|cache_images"}</label></dt>
				<dd>
{foreach $cache_images as $row}
					<label for="cache-images-{$row.value}">
						<input type="radio" name="cache_images" id="cache-images-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt>
					<label for="cache-minify">{lang t="system|minify_cache_lifetime"}</label>
					<span>({lang t="system|minify_cache_lifetime_description"})</span>
				</dt>
				<dd><input type="text" name="cache_minify" id="cache-minify" value="{$form.cache_minify}" maxlength="20"></dd>
			</dl>
		</div>
		<div id="tab-6" class="ui-tabs-hide">
			<dl>
				<dt><label for="mailer-type">{lang t="system|mailer_type"}</label></dt>
				<dd>
					<select name="mailer_type" id="mailer-type">
{foreach $mailer_type as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl id="mailer-smtp-1">
				<dt><label for="mailer-smtp-host">{lang t="system|mailer_smtp_hostname"}</label></dt>
				<dd><input type="text" name="mailer_smtp_host" id="mailer-smtp-host" value="{$form.mailer_smtp_host}"></dd>
				<dt><label for="mailer-smtp-port">{lang t="system|mailer_smtp_port"}</label></dt>
				<dd><input type="number" name="mailer_smtp_port" id="mailer-smtp-port" value="{$form.mailer_smtp_port}"></dd>
				<dt><label for="mailer-smtp-security">{lang t="system|mailer_smtp_security"}</label></dt>
				<dd>
					<select name="mailer_smtp_security" id="mailer-smtp-security">
{foreach $mailer_smtp_security as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="mailer-smtp-auth-1">{lang t="system|mailer_smtp_auth"}</label></dt>
				<dd>
{foreach $mailer_smtp_auth as $row}
					<label for="mailer-smtp-auth-{$row.value}">
						<input type="radio" name="mailer_smtp_auth" id="mailer-smtp-auth-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
			<dl id="mailer-smtp-2">
				<dt><label for="mailer-smtp-user">{lang t="system|mailer_smtp_username"}</label></dt>
				<dd><input type="text" name="mailer_smtp_user" id="mailer-smtp-user" value="{$form.mailer_smtp_user}" maxlength="40"></dd>
				<dt><label for="mailer-smtp-password">{lang t="system|mailer_smtp_password"}</label></dt>
				<dd><input type="password" name="mailer_smtp_password" id="mailer-smtp-password" value="{$form.mailer_smtp_password}"></dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>