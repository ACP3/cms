<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="gallery|latest_galleries"}</h3>
    </div>
    <div class="list-group"
    ">
    {if isset($sidebar_galleries)}
        {foreach $sidebar_galleries as $row}
            <a href="{uri args="gallery/pics/id_`$row.id`"}" class="list-group-item" title="{$row.start} - {$row.title}">{$row.title_short}</a>
        {/foreach}
    {else}
        <span class="list-group-item">{lang t="system|no_entries_short"}</span>
    {/if}
</div></div>