{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="newsletter|newsletter"}</legend>
		<dl>
			<dt><label for="subject">{lang t="newsletter|subject"}</label></dt>
			<dd><input type="text" name="form[subject]" id="subject" value="{$form.subject}" required></dd>
			<dt><label for="text">{lang t="newsletter|text"}</label></dt>
			<dd><textarea name="form[text]" id="text" cols="50" rows="5" required>{$form.text}</textarea></dd>
			<dt><label for="test-1">{lang t="newsletter|test_newsletter"}</label> <span>({lang t="newsletter|test_nl_description"})</span></dt>
			<dd>
{foreach $test as $row}
				<label for="test-{$row.value}">
					<input type="radio" name="form[test]" id="test-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
			<dt>
				<label for="action-1">{lang t="newsletter|action"}</label>
				<span>({lang t="newsletter|action_description"})</span>
			</dt>
			<dd>
{foreach $action as $row}
				<label for="action-{$row.value}">
					<input type="radio" name="form[action]" id="action-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
	</div>
</form>