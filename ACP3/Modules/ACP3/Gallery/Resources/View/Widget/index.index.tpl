<div class="card mb-3">
    <div class="card-header">
        {lang t="gallery|latest_galleries"}
    </div>
    <div class="list-group list-group-flush">
        {if !empty($sidebar_galleries)}
            {foreach $sidebar_galleries as $row}
                <a href="{uri args="gallery/index/pics/id_`$row.id`"}"
                   class="list-group-item list-group-item-action"
                   title="{date_format date=$row.start} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>
