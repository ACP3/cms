<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="news|latest_news"}</h3>
    </div>
    <div class="list-group">
        {if isset($sidebar_news)}
            {foreach $sidebar_news as $row}
                <a href="{uri args="news/details/id_`$row.id`"}" class="list-group-item" title="{$row.start} - {$row.title}">{$row.title_short}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</div>