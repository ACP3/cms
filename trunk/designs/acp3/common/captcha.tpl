<fieldset class="captcha">
	<legend>{lang t="captcha|captcha"}</legend>
	<dl style="text-align:center">
		<dt><img src="{uri args="captcha/image"}" width="{$captcha.width}" height="{$captcha.height}" alt=""></dt>
		<dd>
			<input type="text" name="form[captcha]" id="captcha" value="" style="width:auto;display:inline" required>
		</dd>
	</dl>
</fieldset>