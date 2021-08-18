<div class="card mb-3">
    <div class="card-header">
        {lang t="news|latest_news"}
    </div>
    <div class="list-group list-group-flush">
        {if !empty($sidebar_news)}
            {foreach $sidebar_news as $row}
                <a href="{uri args="news/index/details/id_`$row.id`"}"
                   class="list-group-item list-group-item-action"
                   title="{date_format date=$row.start format=$dateformat} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>
