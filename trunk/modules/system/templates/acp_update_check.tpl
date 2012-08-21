<div class="alert alert-block alert-{$update.class|default:info}">
{if isset($update)}
	<h4 class="alert-heading">{$update.text}</h4>
	<ul style="margin:10px 20px auto">
		<li><strong>{lang t="system|installed_version"}:</strong> {$update.installed_version}</li>
		<li><strong>{lang t="system|current_version"}:</strong> {$update.current_version}</li>
	</ul>
{else}
	<h4 class="alert-heading">{lang t="system|error_update_check"}</h4>
{/if}
</div>