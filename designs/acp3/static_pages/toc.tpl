<div id="toc">
	<h4>{lang t="static_pages|table_of_contents"}</h4>
	<ul>
{foreach $toc as $row}
		<li>{if $row.selected}<span>{$row.title}</span>{else}<a href="{$row.uri}">{$row.title}</a>{/if}</li>
{/foreach}
	</ul>
</div>