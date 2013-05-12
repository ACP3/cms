{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript">
$(document).ready(function() {
	$('#notify').bind('change', function() {
		var $elem = $('#notify-email').parents('.control-group');
		if ($(this).val() == 0) {
			$elem.hide();
		} else {
			$elem.show();
		}
	}).children('option:selected').trigger('change');
});
</script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="date-format" class="control-label">{lang t="system|date_format"}</label>
		<div class="controls">
			<select name="dateformat" id="date-format">
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
		<label for="{$overlay.0.id}" class="control-label">{lang t="guestbook|use_overlay"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $overlay as $row}
				<input type="radio" name="overlay" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{if isset($allow_emoticons)}
	<div class="control-group">
		<label for="{$allow_emoticons.0.id}" class="control-label">{lang t="guestbook|allow_emoticons"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $allow_emoticons as $row}
				<input type="radio" name="emoticons" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{/if}
{if isset($newsletter_integration)}
	<div class="control-group">
		<label for="{$newsletter_integration.0.id}" class="control-label">{lang t="guestbook|newsletter_integration"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $newsletter_integration as $row}
				<input type="radio" name="newsletter_integration" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/guestbook"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>