{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="guestbook|settings"}</legend>
		<dl>
			<dt><label for="date-format">{lang t="common|date_format"}</label></dt>
			<dd>
				<select name="dateformat" id="date-format">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="notify">{lang t="guestbook|notification"}</label></dt>
			<dd>
				<select name="notify" id="notify">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $notify as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="notify-email">{lang t="guestbook|notification_email"}</label></dt>
			<dd><input type="text" name="notify_email" id="notify-email" value="{$form.notify_email}"></dd>
			<dt><label for="overlay-1">{lang t="guestbook|use_overlay"}</label>	</dt>
			<dd>
{foreach $overlay as $row}
				<label for="overlay-{$row.value}">
					<input type="radio" name="overlay" id="overlay-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{if isset($allow_emoticons)}
			<dt><label for="emoticons-1">{lang t="guestbook|allow_emoticons"}</label></dt>
			<dd>
{foreach $allow_emoticons as $row}
				<label for="emoticons-{$row.value}">
					<input type="radio" name="emoticons" id="emoticons-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{/if}
{if isset($newsletter_integration)}
			<dt><label for="newsletter-integration-1">{lang t="guestbook|newsletter_integration"}</label></dt>
			<dd>
{foreach $newsletter_integration as $row}
				<label for="newsletter-integration-{$row.value}">
					<input type="radio" name="newsletter_integration" id="newsletter-integration-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{/if}
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>