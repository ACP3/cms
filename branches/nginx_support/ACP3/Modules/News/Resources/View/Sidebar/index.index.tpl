<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="news|latest_news"}</h3>
    </div>
    <div class="list-group">
        {if !empty($sidebar_news)}
            {foreach $sidebar_news as $row}
                <a href="{uri args="news/index/details/id_`$row.id`"}" class="list-group-item" title="{date_format date=$row.start format=$dateformat} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>