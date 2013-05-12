{if isset($galleries)}
{$pagination}
{foreach $galleries as $row}
<div class="dataset-box">
	<div class="header">
		<small class="pull-right">
			<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
		</small>
		<a href="{uri args="gallery/pics/id_`$row.id`"}">{$row.title} ({$row.pics_lang})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}