<nav id="toc" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="system|table_of_contents"}</h3>
    </div>

    <div class="list-group">
        {foreach $toc as $row}
            <a href="{$row.uri}" class="list-group-item{if $row.selected} active{/if}">{$row.title}</a>
        {/foreach}
    </div>
</nav>