<nav id="toc" class="toc panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">{lang t="system|table_of_contents"}</h2>
    </div>

    <div class="list-group">
        {foreach $toc as $row}
            <a href="{$row.uri}" class="list-group-item{if $row.selected} active{/if}">{$row.title}</a>
        {/foreach}
    </div>
</nav>
