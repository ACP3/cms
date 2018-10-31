<div class="card mb-3">
    <div class="card-header">
        {lang t="news|latest_news"}
    </div>
    <div class="list-group list-group-flush">
        {if !empty($sidebar_news)}
            {foreach $sidebar_news as $row}
                <a href="{uri args="news/index/details/id_`$row.id`"}"
                   class="list-group-item text-truncate"
                   title="{date_format date=$row.start format=$dateformat} - {$row.title}">{$row.title}</a>
            {/foreach}
        {else}
            <div class="list-group-item">{lang t="system|no_entries_short"}</div>
        {/if}
    </div>
</div>
