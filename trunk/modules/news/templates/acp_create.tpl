{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="news|news"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="news|hyperlink"}</a></li>
			<li><a href="#tab-4" data-toggle="tab">{lang t="common|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="headline" class="control-label">{lang t="news|headline"}</label>
					<div class="controls"><input type="text" name="headline" id="headline" value="{$form.headline}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="text" class="control-label">{lang t="news|text"}</label>
					<div class="controls">{wysiwyg name="text" value="`$form.text`" height="250"}</div>
				</div>
				<div class="control-group">
					<label for="cat" class="control-label">{lang t="common|category"}</label>
					<div class="controls">{$categories}</div>
				</div>
{if isset($options)}
				<div class="control-group">
					<label for="{$options.0.name}" class="control-label">{lang t="common|options"}</label>
					<div class="controls">
{foreach $options as $row}
						<label for="{$row.name}" class="checkbox inline">
							<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
							{$row.lang}
						</label>
{/foreach}
					</div>
				</div>
{/if}
			</div>
			<div id="tab-3" class="tab-pane">
				<div class="control-group">
					<label for="link-title" class="control-label">{lang t="news|link_title"}</label>
					<div class="controls"><input type="text" name="link_title" id="link-title" value="{$form.link_title}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="uri" class="control-label">{lang t="news|uri"}</label>
					<div class="controls"><input type="url" name="uri" id="uri" value="{$form.uri}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="target" class="control-label">{lang t="news|target_page"}</label>
					<div class="controls">
						<select name="target" id="target">
{foreach $target as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-4" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>