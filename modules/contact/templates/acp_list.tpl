{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="acp/contact"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="address" class="control-label">{lang t="contact|address"}</label>
		<div class="controls">{wysiwyg name="address" value="`$form.address`" height="150" toolbar="simple"}</div>
	</div>
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="common|email"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="telephone" class="control-label">{lang t="contact|telephone"}</label>
		<div class="controls"><input type="tel" name="telephone" id="telephone" value="{$form.telephone}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="fax" class="control-label">{lang t="contact|fax"}</label>
		<div class="controls"><input type="tel" name="fax" id="fax" value="{$form.fax}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="disclaimer" class="control-label">{lang t="contact|disclaimer"}</label>
		<div class="controls">{wysiwyg name="disclaimer" value="`$form.disclaimer`" height="150px" toolbar="simple"}</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/contact"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>