{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript">
$(function() {
	$('input[name="mailer_smtp_auth"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#mailer-smtp-2').show();
		} else {
			$('#mailer-smtp-2').hide();
		}
	});
	$('#mailer-type').bind('change', function() {
		if ($(this).val() == 'smtp') {
			$('#mailer-smtp-1').show();
			$('input[name="mailer_smtp_auth"]:checked').trigger('click');
		} else {
			$('#mailer-smtp-1, #mailer-smtp-2').hide();
		}
	});

	$('#mailer-type').trigger('change');
});
</script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="common|date"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="system|acp_maintenance"}</a></li>
			<li><a href="#tab-4" data-toggle="tab">{lang t="common|seo"}</a></li>
			<li><a href="#tab-5" data-toggle="tab">{lang t="system|performance"}</a></li>
			<li><a href="#tab-6" data-toggle="tab">{lang t="system|email"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
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
						<input type="number" name="flood" id="flood" value="{$form.flood}">
						<p class="help-block">{lang t="system|flood_barrier_description"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="homepage" class="control-label">{lang t="system|homepage"}</label>
					<div class="controls">
						<input type="text" name="homepage" id="homepage" value="{$form.homepage}">
						<p class="help-block">{lang t="system|homepage_description"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="wysiwyg" class="control-label">{lang t="system|editor"}</label>
					<div class="controls">
						<select name="wysiwyg" id="wysiwyg">
{foreach $wysiwyg as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
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
			<div id="tab-3" class="tab-pane">
				<div class="control-group">
					<label for="maintenance-mode-1" class="control-label">{lang t="system|maintenance_mode"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $maintenance as $row}
							<input type="radio" name="maintenance_mode" id="maintenance-mode-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="maintenance-mode-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="maintenance-message" class="control-label">{lang t="system|maintenance_msg"}</label>
					<div class="controls"><textarea name="maintenance_message" id="maintenance-message" cols="50" rows="6" class="span6">{$form.maintenance_message}</textarea></div>
				</div>
			</div>
			<div id="tab-4" class="tab-pane">
				<div class="control-group">
					<label for="seo-title" class="control-label">{lang t="system|title"}</label>
					<div class="controls"><input type="text" name="seo_title" id="seo-title" value="{$form.seo_title}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="seo-meta-description" class="control-label">{lang t="common|seo_description"}</label>
					<div class="controls"><input type="text" name="seo_meta_description" id="seo-meta-description" value="{$form.seo_meta_description}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="seo-meta-keywords" class="control-label">{lang t="common|seo_keywords"}</label>
					<div class="controls">
						<textarea name="seo_meta_keywords" id="seo-meta-keywords" cols="50" rows="6" class="span6">{$form.seo_meta_keywords}</textarea>
						<p class="help-block">{lang t="common|seo_keywords_separate_with_commas"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="seo-robots" class="control-label">{lang t="common|seo_robots"}</label>
					<div class="controls">
						<select name="seo_robots" id="seo-robots">
{foreach $robots as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="seo-aliases-1" class="control-label">{lang t="system|enable_seo_aliases"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $aliases as $row}
							<input type="radio" name="seo_aliases" id="seo-aliases-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="seo-aliases-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="seo-mod-rewrite-1" class="control-label">{lang t="system|mod_rewrite"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $mod_rewrite as $row}
							<input type="radio" name="seo_mod_rewrite" id="seo-mod-rewrite-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="seo-mod-rewrite-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
						<p class="help-block">{lang t="system|mod_rewrite_description"}</p>
					</div>
				</div>
			</div>
			<div id="tab-5" class="tab-pane">
				<div class="control-group">
					<label for="cache-images-1" class="control-label">{lang t="system|cache_images"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $cache_images as $row}
							<input type="radio" name="cache_images" id="cache-images-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="cache-images-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="cache-minify" class="control-label">{lang t="system|minify_cache_lifetime"}</label>
					<div class="controls">
						<input type="text" name="cache_minify" id="cache-minify" value="{$form.cache_minify}" maxlength="20">
						<p class="help-block">{lang t="system|minify_cache_lifetime_description"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="extra-css" class="control-label">{lang t="system|additional_stylesheets"}</label>
					<div class="controls">
						<input type="text" name="extra_css" id="extra-css" value="{$form.extra_css}" class="span6">
						<p class="help-block">{lang t="system|additional_stylesheets_description"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="extra-js" class="control-label">{lang t="system|additional_javascript_files"}</label>
					<div class="controls">
						<input type="text" name="extra_js" id="extra-js" value="{$form.extra_js}" class="span6">
						<p class="help-block">{lang t="system|additional_javascript_files_description"}</p>
					</div>
				</div>
			</div>
			<div id="tab-6" class="tab-pane">
				<div class="control-group">
					<label for="mailer-type" class="control-label">{lang t="system|mailer_type"}</label>
					<div class="controls">
						<select name="mailer_type" id="mailer-type">
{foreach $mailer_type as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div id="mailer-smtp-1">
					<div class="control-group">
						<label for="mailer-smtp-host" class="control-label">{lang t="system|mailer_smtp_hostname"}</label>
						<div class="controls"><input type="text" name="mailer_smtp_host" id="mailer-smtp-host" value="{$form.mailer_smtp_host}"></div>
					</div>
					<div class="control-group">
						<label for="mailer-smtp-port" class="control-label">{lang t="system|mailer_smtp_port"}</label>
						<div class="controls"><input type="number" name="mailer_smtp_port" id="mailer-smtp-port" value="{$form.mailer_smtp_port}"></div>
					</div>
					<div class="control-group">
						<label for="mailer-smtp-security" class="control-label">{lang t="system|mailer_smtp_security"}</label>
						<div class="controls">
							<select name="mailer_smtp_security" id="mailer-smtp-security">
{foreach $mailer_smtp_security as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
							</select>
						</div>
					</div>
					<div class="control-group">
						<label for="mailer-smtp-auth-1" class="control-label">{lang t="system|mailer_smtp_auth"}</label>
						<div class="controls">
							<div class="btn-group" data-toggle="radio">
{foreach $mailer_smtp_auth as $row}
								<input type="radio" name="mailer_smtp_auth" id="mailer-smtp-auth-{$row.value}" value="{$row.value}"{$row.checked}>
								<label for="mailer-smtp-auth-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}

							</div>
						</div>
					</div>
					<div id="mailer-smtp-2">
						<div class="control-group">
							<label for="mailer-smtp-user" class="control-label">{lang t="system|mailer_smtp_username"}</label>
							<div class="controls"><input type="text" name="mailer_smtp_user" id="mailer-smtp-user" value="{$form.mailer_smtp_user}" maxlength="40"></div>
						</div>
						<div class="control-group">
							<label for="mailer-smtp-password" class="control-label">{lang t="system|mailer_smtp_password"}</label>
							<div class="controls"><input type="password" name="mailer_smtp_password" id="mailer-smtp-password" value="{$form.mailer_smtp_password}"></div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		{$form_token}
	</div>
</form>