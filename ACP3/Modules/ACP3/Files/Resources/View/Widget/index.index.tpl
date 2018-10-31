<div class="card mb-3">
    <div class="card-header">
        {lang t="files|latest_files"}
    </div>
    <div class="list-group list-group-flush">
        {if !empty($sidebar_files)}
            {foreach $sidebar_files as $row}
                <a href="{uri args="files/index/details/id_`$row.id`"}"
                   class="list-group-item text-truncate"
                   title="{date_format date=$row.start} - {$row.title}">{$row.title}</a>
            {/foreach}
        {else}
            <div class="list-group-item">{lang t="system|no_entries_short"}</div>
        {/if}
    </div>
</div>
