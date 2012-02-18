<div class="error-box">
{if isset($update)}
	<h4>{$update_text}</h4>
	<ul style="margin-bottom:0">
		<li><strong>{lang t="system|installed_version"}:</strong> {$update.2}</li>
		<li><strong>{lang t="system|current_version"}:</strong> {$update.0}</li>
	</ul>
{else}
	<h5>{lang t="system|error_update_check"}</h5>
{/if}
</div>