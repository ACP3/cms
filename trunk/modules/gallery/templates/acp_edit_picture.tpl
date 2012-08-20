{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="file" class="control-label">{lang t="gallery|select_new_picture"}</label>
		<div class="controls"><input type="file" name="file" id="file"></div>
	</div>
	<div class="control-group">
		<label for="description" class="control-label">{lang t="common|description"}</label>
		<div class="controls">{wysiwyg name="description" value="`$form.description`" height="150" toolbar="simple"}</div>
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
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/gallery/edit/id_`$gallery_id`"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>