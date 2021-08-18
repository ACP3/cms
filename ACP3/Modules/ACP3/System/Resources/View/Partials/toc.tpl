<nav id="toc" class="card mb-3 table-of-contents">
    <div class="card-header">
        {lang t="system|table_of_contents"}
    </div>
    <div class="list-group list-group-flush">
        {foreach $toc as $row}
            <a href="{$row.uri}"
               class="list-group-item list-group-item-action{if $row.selected} active{/if}"{if $row.selected} aria-current="true"{/if}>{$row.title}</a>
        {/foreach}
    </div>
</nav>
