<div class="alert alert-block">
{if isset($update)}
	<h4 class="alert-heading">{$update_text}</h4>
	<ul style="margin:10px 20px auto">
		<li><strong>{lang t="system|installed_version"}:</strong> {$update.2}</li>
		<li><strong>{lang t="system|current_version"}:</strong> {$update.0}</li>
	</ul>
{else}
	<h4 class="alert-heading">{lang t="system|error_update_check"}</h4>
{/if}
</div>