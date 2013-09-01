{if $captcha.input_only === true}
	<img src="{uri args="captcha/image"}" width="{$captcha.width}" height="{$captcha.height}" alt=""><br>
	<input type="text" name="captcha" id="{$captcha.id}" value="" required>
{else}
	<div class="control-group">
		<label for="{$captcha.id}" class="control-label">{lang t="captcha|captcha"}</label>
		<div class="controls">
			<img src="{uri args="captcha/image"}" width="{$captcha.width}" height="{$captcha.height}" alt=""><br>
			<input type="text" name="captcha" id="{$captcha.id}" value="" required>
		</div>
	</div>
{/if}