{if isset($files)}
{foreach $files as $row}
<div class="dataset-box">
	<div class="header">
		<small class="pull-right">
			<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
		</small>
		<a href="{uri args="files/details/id_`$row.id`"}">{$row.title} ({$row.size})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}