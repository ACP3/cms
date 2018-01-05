<section class="panel panel-default">
    <header class="panel-heading">
        <h2 class="panel-title">{lang t="files|latest_files"}</h2>
    </header>
    <div class="list-group">
        {if !empty($sidebar_files)}
            {foreach $sidebar_files as $row}
                <a href="{uri args="files/index/details/id_`$row.id`"}" class="list-group-item" title="{date_format date=$row.start} - {$row.title}">{$row.title|truncate:30}</a>
            {/foreach}
        {else}
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        {/if}
    </div>
</section>
