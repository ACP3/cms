<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="gallery|latest_galleries"}</h3>
    </div>
    <div class="list-group">
        {if !empty($sidebar_galleries)}
            {foreach $sidebar_galleries as $row}
                <a href="{uri args="gallery/index/pics/id_`$row.id`"}" class="list-group-item" title="{date_format date=$row.start} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>