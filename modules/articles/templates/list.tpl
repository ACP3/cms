{if isset($articles)}
{$pagination}
{foreach $articles as $row}
<div class="dataset-box">
	<div class="header">
		<div class="small pull-right">{$row.date}</div>
		<a href="{uri args="articles/details/id_`$row.id`"}">{$row.title}</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}