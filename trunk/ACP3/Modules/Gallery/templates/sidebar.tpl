<ul class="nav nav-list">
	<li class="nav-header">{lang t="gallery|latest_galleries"}</li>
{if isset($sidebar_galleries)}
{foreach $sidebar_galleries as $row}
	<li><a href="{uri args="gallery/pics/id_`$row.id`"}" title="{$row.start} - {$row.title}">{$row.title_short}</a></li>
{/foreach}
{else}
	<li>{lang t="system|no_entries_short"}</li>
{/if}
</ul>