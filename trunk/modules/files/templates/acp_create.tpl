{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}js/files_admin.js"></script>
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="files|file_statements"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="common|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="link-title" class="control-label">{lang t="files|link_title"}</label>
					<div class="controls"><input type="text" name="link_title" id="link-title" value="{$form.link_title}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="file-internal" class="control-label">{lang t="files|filename"}</label>
					<div class="controls">
						<label for="external" class="checkbox">
							<input type="checkbox" name="external" id="external" value="1"{$checked_external}>
							{lang t="files|external_resource"}
						</label>
						<input type="file" name="file_internal" id="file-internal">
						<input type="url" name="file_external" id="file-external" value="{$form.file_external}" maxlength="120">
					</div>
				</div>
				<div id="external-filesize" class="control-group">
					<label for="filesize" class="control-label">{lang t="files|filesize"}</label>
					<div class="controls">
						<input type="text" name="filesize" id="filesize" value="{$form.filesize}" maxlength="15">
						<select name="unit" id="unit">
{foreach $units as $row}
							<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="text" class="control-label">{lang t="common|description"}</label>
					<div class="controls">{wysiwyg name="text" value="`$form.text`" height="200" toolbar="simple"}</div>
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
						<label for="{$row.name}" class="checkbox">
							<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
							{$row.lang}
						</label>
{/foreach}
					</div>
				</div>
{/if}
			</div>
			<div id="tab-3" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/files"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>