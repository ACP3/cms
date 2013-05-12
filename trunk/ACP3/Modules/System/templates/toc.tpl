<nav id="toc" class="well well-small">
	<h4>{lang t="system|table_of_contents"}</h4>
	<ul class="nav nav-list">
{foreach $toc as $row}
		<li{if $row.selected} class="active"{/if}><a href="{$row.uri}">{$row.title}</a></li>
{/foreach}
	</ul>
</nav>