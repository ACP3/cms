<div id="pagination">
	{lang t="common|pagination"}:
{foreach $pagination as $row}
{if $row.selected}
	<span>{$row.page}</span>
{else}
	<a href="{$row.uri}"{if isset($row.title)} title="{$row.title}"{/if}>{$row.page}</a>
{/if}
{/foreach}
</div>
<hr>