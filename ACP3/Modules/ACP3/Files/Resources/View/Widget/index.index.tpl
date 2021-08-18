<div class="card mb-3">
    <div class="card-header">
        {lang t="files|latest_files"}
    </div>
    <div class="list-group list-group-flush">
        {if !empty($sidebar_files)}
            {foreach $sidebar_files as $row}
                <a href="{uri args="files/index/details/id_`$row.id`"}"
                   class="list-group-item list-group-item-action"
                   title="{date_format date=$row.start} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>
