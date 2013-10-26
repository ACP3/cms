{if $captcha.input_only === true}
	<div class="input-group input-group-lg">
		<span class="input-group-addon" style="padding-top: 5px; padding-bottom: 5px"><img src="{uri args="captcha/image"}" width="{$captcha.width}" height="{$captcha.height}" alt=""></span>
		<input class="form-control input-lg" type="text" name="captcha" id="{$captcha.id}" value="" required>
	</div>
{else}
	<div class="form-group">
		<label for="{$captcha.id}" class="col-lg-2 control-label">{lang t="captcha|captcha"}</label>
		<div class="col-lg-3">
			<div class="input-group input-group-lg">
				<span class="input-group-addon" style="padding-top: 5px; padding-bottom: 5px"><img src="{uri args="captcha/image"}" width="{$captcha.width}" height="{$captcha.height}" alt=""></span>
				<input class="form-control input-lg" type="text" name="captcha" id="{$captcha.id}" value="" required>
			</div>
		</div>
	</div>
{/if}