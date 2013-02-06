{if isset($articles)}
{$pagination}
{foreach $articles as $row}
<div class="dataset-box">
	<div class="header">
		<small class="pull-right">
			<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
		</small>
		<a href="{uri args="articles/details/id_`$row.id`"}">{$row.title}</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}