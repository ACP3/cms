{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}newsletter/script.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="newsletter|newsletter"}</legend>
		<dl>
			<dt><label for="subject">{lang t="newsletter|subject"}</label></dt>
			<dd><input type="text" name="subject" id="subject" value="{$form.subject}" required></dd>
		</dl>
		<dl>
			<dt><label for="text">{lang t="newsletter|text"}</label></dt>
			<dd><textarea name="text" id="text" cols="50" rows="5" required>{$form.text}</textarea></dd>
		</dl>
		<dl>
			<dt>
				<label for="action-1">{lang t="newsletter|action"}</label>
				<span>({lang t="newsletter|action_description"})</span>
			</dt>
			<dd>
{foreach $action as $row}
				<label for="action-{$row.value}">
					<input type="radio" name="action" id="action-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
		<dl id="test-newsletter">
			<dt>
				<label for="test-1">{lang t="newsletter|test_newsletter"}</label>
				<span>({lang t="newsletter|test_nl_description"})</span>
			</dt>
			<dd>
{foreach $test as $row}
				<label for="test-{$row.value}">
					<input type="radio" name="test" id="test-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>