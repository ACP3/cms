{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="gallery|picture_information"}</legend>
		<div class="control-group">
			<label for="file" class="control-label">{lang t="gallery|select_picture"}</label>
			<div class="controls"><input type="file" name="file" id="file" required></div>
		</div>
		<div class="control-group">
			<label for="description" class="control-label">{lang t="common|description"}</label>
			<div class="controls">{wysiwyg name="description" value="`$form.description`" height="150" toolbar="simple"}</div>
		</div>
{if isset($options)}
		<div class="control-group">
			<label for="{$options.0.name}" class="control-label">{lang t="common|options"}</label>
			<div class="controls">
				<ul class="unstyled">
{foreach $options as $row}
					<li>
						<label for="{$row.name}" class="checkbox">
							<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
							{$row.lang}
						</label>
					</li>
{/foreach}
				</ul>
			</div>
		</div>
{/if}
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>