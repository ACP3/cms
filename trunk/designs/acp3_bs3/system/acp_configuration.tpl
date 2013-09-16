{if isset($redirect_message)}
	{$redirect_message}
{/if}
{if isset($error_msg)}
	{$error_msg}
{/if}
<script type="text/javascript">
	$(function() {
		$('input[name="mailer_smtp_auth"]').bind('click', function() {
			var $elem = $('#mailer-smtp-2');
			if ($(this).val() == 1) {
				$elem.show();
			} else {
				$elem.hide();
			}
		}).filter(':checked').trigger('click');

		$('#mailer-type').bind('change', function() {
			if ($(this).val() === 'smtp') {
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
			<li><a href="#tab-2" data-toggle="tab">{lang t="system|date"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="system|acp_maintenance"}</a></li>
			<li><a href="#tab-4" data-toggle="tab">{lang t="system|seo"}</a></li>
			<li><a href="#tab-5" data-toggle="tab">{lang t="system|performance"}</a></li>
			<li><a href="#tab-6" data-toggle="tab">{lang t="system|email"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="form-group">
					<label for="homepage" class="col-lg-2 control-label">{lang t="system|homepage"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="homepage" id="homepage" value="{$form.homepage}">
						<p class="help-block">{lang t="system|homepage_description"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="entries" class="col-lg-2 control-label">{lang t="system|records_per_page"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="entries" id="entries">
							{foreach $entries as $row}
								<option value="{$row.value}"{$row.selected}>{$row.value}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="flood" class="col-lg-2 control-label">{lang t="system|flood_barrier"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="flood" id="flood" value="{$form.flood}">
						<p class="help-block">{lang t="system|flood_barrier_description"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="icons-path" class="col-lg-2 control-label">{lang t="system|path_to_icons"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="icons_path" id="icons-path" value="{$form.icons_path}"></div>
				</div>
				<div class="form-group">
					<label for="wysiwyg" class="col-lg-2 control-label">{lang t="system|editor"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="wysiwyg" id="wysiwyg">
							{foreach $wysiwyg as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="form-group">
					<label for="date-format-long" class="col-lg-2 control-label">{lang t="system|date_format_long"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20">
						<p class="help-block">{lang t="system|php_date_function"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="date-format-short" class="col-lg-2 control-label">{lang t="system|date_format_short"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></div>
				</div>
				<div class="form-group">
					<label for="date-time-zone" class="col-lg-2 control-label">{lang t="system|time_zone"}</label>
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
			<div id="tab-3" class="tab-pane">
				<div class="form-group">
					<label for="{$maintenance.0.id}" class="col-lg-2 control-label">{lang t="system|maintenance_mode"}</label>
					<div class="col-lg-10">
						<div class="btn-group" data-toggle="buttons">
							{foreach $maintenance as $row}
								<label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
									<input type="radio" name="maintenance_mode" id="{$row.id}" value="{$row.value}"{$row.checked}>
									{$row.lang}
								</label>
							{/foreach}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="maintenance-message" class="col-lg-2 control-label">{lang t="system|maintenance_msg"}</label>
					<div class="col-lg-10"><textarea class="form-control" name="maintenance_message" id="maintenance-message" cols="50" rows="6" class="span6">{$form.maintenance_message}</textarea></div>
				</div>
			</div>
			<div id="tab-4" class="tab-pane">
				<div class="form-group">
					<label for="seo-title" class="col-lg-2 control-label">{lang t="system|title"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="seo_title" id="seo-title" value="{$form.seo_title}" maxlength="120"></div>
				</div>
				<div class="form-group">
					<label for="seo-meta-description" class="col-lg-2 control-label">{lang t="system|seo_description"}</label>
					<div class="col-lg-10"><input class="form-control" type="text" name="seo_meta_description" id="seo-meta-description" value="{$form.seo_meta_description}" maxlength="120"></div>
				</div>
				<div class="form-group">
					<label for="seo-meta-keywords" class="col-lg-2 control-label">{lang t="system|seo_keywords"}</label>
					<div class="col-lg-10">
						<textarea class="form-control" name="seo_meta_keywords" id="seo-meta-keywords" cols="50" rows="6" class="span6">{$form.seo_meta_keywords}</textarea>
						<p class="help-block">{lang t="system|seo_keywords_separate_with_commas"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="seo-robots" class="col-lg-2 control-label">{lang t="system|seo_robots"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="seo_robots" id="seo-robots">
							{foreach $robots as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="{$aliases.0.id}" class="col-lg-2 control-label">{lang t="system|enable_seo_aliases"}</label>
					<div class="col-lg-10">
						<div class="btn-group" data-toggle="buttons">
							{foreach $aliases as $row}
								<label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
									<input type="radio" name="seo_aliases" id="{$row.id}" value="{$row.value}"{$row.checked}>
									{$row.lang}
								</label>
							{/foreach}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="{$mod_rewrite.0.id}" class="col-lg-2 control-label">{lang t="system|mod_rewrite"}</label>
					<div class="col-lg-10">
						<div class="btn-group" data-toggle="buttons">
							{foreach $mod_rewrite as $row}
								<label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
									<input type="radio" name="seo_mod_rewrite" id="{$row.id}" value="{$row.value}"{$row.checked}>
									{$row.lang}
								</label>
							{/foreach}
						</div>
						<p class="help-block">{lang t="system|mod_rewrite_description"}</p>
					</div>
				</div>
			</div>
			<div id="tab-5" class="tab-pane">
				<div class="form-group">
					<label for="{$cache_images.0.id}" class="col-lg-2 control-label">{lang t="system|cache_images"}</label>
					<div class="col-lg-10">
						<div class="btn-group" data-toggle="buttons">
							{foreach $cache_images as $row}
								<label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
									<input type="radio" name="cache_images" id="{$row.id}" value="{$row.value}"{$row.checked}>
									{$row.lang}
								</label>
							{/foreach}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="cache-minify" class="col-lg-2 control-label">{lang t="system|minify_cache_lifetime"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="cache_minify" id="cache-minify" value="{$form.cache_minify}" maxlength="20">
						<p class="help-block">{lang t="system|minify_cache_lifetime_description"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="extra-css" class="col-lg-2 control-label">{lang t="system|additional_stylesheets"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="extra_css" id="extra-css" value="{$form.extra_css}" class="span6">
						<p class="help-block">{lang t="system|additional_stylesheets_description"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="extra-js" class="col-lg-2 control-label">{lang t="system|additional_javascript_files"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="text" name="extra_js" id="extra-js" value="{$form.extra_js}" class="span6">
						<p class="help-block">{lang t="system|additional_javascript_files_description"}</p>
					</div>
				</div>
			</div>
			<div id="tab-6" class="tab-pane">
				<div class="form-group">
					<label for="mailer-type" class="col-lg-2 control-label">{lang t="system|mailer_type"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="mailer_type" id="mailer-type">
							{foreach $mailer_type as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div id="mailer-smtp-1">
					<div class="form-group">
						<label for="mailer-smtp-host" class="col-lg-2 control-label">{lang t="system|mailer_smtp_hostname"}</label>
						<div class="col-lg-10"><input class="form-control" type="text" name="mailer_smtp_host" id="mailer-smtp-host" value="{$form.mailer_smtp_host}"></div>
					</div>
					<div class="form-group">
						<label for="mailer-smtp-port" class="col-lg-2 control-label">{lang t="system|mailer_smtp_port"}</label>
						<div class="col-lg-10"><input class="form-control" type="number" name="mailer_smtp_port" id="mailer-smtp-port" value="{$form.mailer_smtp_port}"></div>
					</div>
					<div class="form-group">
						<label for="mailer-smtp-security" class="col-lg-2 control-label">{lang t="system|mailer_smtp_security"}</label>
						<div class="col-lg-10">
							<select class="form-control" name="mailer_smtp_security" id="mailer-smtp-security">
								{foreach $mailer_smtp_security as $row}
									<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="{$mailer_smtp_auth.0.id}" class="col-lg-2 control-label">{lang t="system|mailer_smtp_auth"}</label>
						<div class="col-lg-10">
							<div class="btn-group" data-toggle="buttons">
								{foreach $mailer_smtp_auth as $row}
									<label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
										<input type="radio" name="mailer_smtp_auth" id="{$row.id}" value="{$row.value}"{$row.checked}>
										{$row.lang}
									</label>
								{/foreach}
							</div>
						</div>
					</div>
					<div id="mailer-smtp-2">
						<div class="form-group">
							<label for="mailer-smtp-user" class="col-lg-2 control-label">{lang t="system|mailer_smtp_username"}</label>
							<div class="col-lg-10"><input class="form-control" type="text" name="mailer_smtp_user" id="mailer-smtp-user" value="{$form.mailer_smtp_user}" maxlength="40"></div>
						</div>
						<div class="form-group">
							<label for="mailer-smtp-password" class="col-lg-2 control-label">{lang t="system|mailer_smtp_password"}</label>
							<div class="col-lg-10"><input class="form-control" type="password" name="mailer_smtp_password" id="mailer-smtp-password" value="{$form.mailer_smtp_password}"></div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			{$form_token}
		</div>
	</div>
</form>