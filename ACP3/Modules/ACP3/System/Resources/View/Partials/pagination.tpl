{if !empty($pagination)}
    <nav class="text-center" aria-label="{lang t="system|pagination"}">
        <ul class="pagination">
            {foreach $pagination as $row}
                <li class="page-item{if $row.selected} active{/if}">
                    <a href="{$row.uri}"
                       {if !empty($row.selector)} class="{$row.selector}"{else} class="page-link"{/if}
                       {if !empty($row.title)} title="{$row.title}" aria-label="{$row.title}"{/if}>{$row.page}</a>
                </li>
            {/foreach}
        </ul>
    </nav>
{/if}
