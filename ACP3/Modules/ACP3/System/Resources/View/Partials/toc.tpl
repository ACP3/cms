<nav id="toc" class="card">
    <div class="card-header p-2">
        <h3 class="h6 card-title mb-0">{lang t="system|table_of_contents"}</h3>
    </div>
    <div class="list-group list-group-flush">
        {foreach $toc as $row}
            <a href="{$row.uri}" class="list-group-item p-2{if $row.selected} active{/if}">{$row.title}</a>
        {/foreach}
    </div>
</nav>
