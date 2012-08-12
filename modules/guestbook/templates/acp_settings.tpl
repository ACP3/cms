{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="guestbook|acp_settings"}</legend>
		<div class="control-group">
			<label for="date-format" class="control-label">{lang t="common|date_format"}</label>
			<div class="controls">
				<select name="dateformat" id="date-format">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</div>
		</div>
		<div class="control-group">
			<label for="notify" class="control-label">{lang t="guestbook|notification"}</label>
			<div class="controls">
				<select name="notify" id="notify">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $notify as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</div>
		</div>
		<div class="control-group">
			<label for="notify-email" class="control-label">{lang t="guestbook|notification_email"}</label>
			<div class="controls"><input type="text" name="notify_email" id="notify-email" value="{$form.notify_email}"></div>
		</div>
		<div class="control-group">
			<label for="overlay-1" class="control-label">{lang t="guestbook|use_overlay"}</label>	</dt>
			<div class="controls">
{foreach $overlay as $row}
				<label for="overlay-{$row.value}" class="checkbox inline">
					<input type="radio" name="overlay" id="overlay-{$row.value}" value="{$row.value}"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</div>
		</div>
{if isset($allow_emoticons)}
		<div class="control-group">
			<label for="emoticons-1" class="control-label">{lang t="guestbook|allow_emoticons"}</label>
			<div class="controls">
{foreach $allow_emoticons as $row}
				<label for="emoticons-{$row.value}" class="checkbox inline">
					<input type="radio" name="emoticons" id="emoticons-{$row.value}" value="{$row.value}"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</div>
		</div>
{/if}
{if isset($newsletter_integration)}
		<div class="control-group">
			<label for="newsletter-integration-1" class="control-label">{lang t="guestbook|newsletter_integration"}</label>
			<div class="controls">
{foreach $newsletter_integration as $row}
				<label for="newsletter-integration-{$row.value}" class="checkbox inline">
					<input type="radio" name="newsletter_integration" id="newsletter-integration-{$row.value}" value="{$row.value}"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</div>
		</div>
{/if}
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>