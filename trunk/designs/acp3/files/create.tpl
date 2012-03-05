{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}files/script.js"></script>
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2">{lang t="files|file_statements"}</a></li>
			<li><a href="#tab-3">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="start">{lang t="common|publication_period"}</label></dt>
				<dd>{$publication_period}</dd>
			</dl>
			<p>
				{lang t="common|date_description"}
			</p>
		</div>
		<div id="tab-2">
			<dl>
				<dt><label for="link-title">{lang t="files|link_title"}</label></dt>
				<dd><input type="text" name="link_title" id="link-title" value="{$form.link_title}" maxlength="120"></dd>
			</dl>
			<dl>
				<dt><label for="file-internal">{lang t="files|filename"}</label></dt>
				<dd>
					<label for="external">
						<input type="checkbox" name="external" id="external" value="1" class="checkbox"{$checked_external}>
						{lang t="files|external_resource"}
					</label>
					<input type="file" name="file_internal" id="file-internal">
					<input type="url" name="file_external" id="file-external" value="{$form.file_external}" maxlength="120">
				</dd>
			</dl>
			<dl id="external-filesize">
				<dt><label for="filesize">{lang t="files|filesize"}</label></dt>
				<dd>
					<input type="text" name="filesize" id="filesize" value="{$form.filesize}" maxlength="15" style="width:49%;display:inline">
					<select name="unit" id="unit" style="width:49%;display:inline">
{foreach $units as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
				<dt><label for="text">{lang t="common|description"}</label></dt>
				<dd>{wysiwyg name="text" value="`$form.text`" height="200" toolbar="simple"}</dd>
			</dl>
			<dl>
				<dt><label for="cat">{lang t="common|category"}</label></dt>
				<dd>
					{$categories}
				</dd>
			</dl>
{if isset($options)}
			<dl>
				<dt><label for="{$options.0.name}">{lang t="common|options"}</label></dt>
				<dd>
					<ul style="margin:0 20px;list-style:none">
{foreach $options as $row}
						<li>
							<label for="{$row.name}">
								<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1" class="checkbox"{$row.checked}>
								{$row.lang}
							</label>
						</li>
{/foreach}
					</ul>
				</dd>
			</dl>
{/if}
		</div>
		<div id="tab-3">
			{$SEO_FORM_FIELDS}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>