{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="gallery|picture_information"}</legend>
		<dl>
			<dt><label for="file">{lang t="gallery|select_picture"}</label></dt>
			<dd><input type="file" name="file" id="file" required></dd>
		</dl>
		<dl>
			<dt><label for="description">{lang t="common|description"}</label></dt>
			<dd>{wysiwyg name="description" value="`$form.description`" height="150" toolbar="simple"}</dd>
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
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>