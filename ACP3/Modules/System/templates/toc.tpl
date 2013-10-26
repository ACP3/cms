<nav id="toc" class="well well-small">
	<h4>{lang t="system|table_of_contents"}</h4>
	<div class="list-group">
{foreach $toc as $row}
		<a href="{$row.uri}" class="list-group-item{if $row.selected} active{/if}">{$row.title}</a>
{/foreach}
	</div>
</nav>