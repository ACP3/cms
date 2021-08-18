{if !empty($pagination)}
    <nav aria-label="{lang t="system|pagination"}">
        <ul class="pagination justify-content-center">
            {foreach $pagination as $row}
                <li class="page-item {if $row.selected} active{/if}" {if $row.selected}aria-current="page"{/if}>
                    <a href="{$row.uri}"
                       class="{if !empty($row.selector)}{$row.selector}{else}page-link{/if}"
                       {if !empty($row.title)} title="{$row.title}" aria-label="{$row.title}"{/if}>{$row.page}</a>
                </li>
            {/foreach}
        </ul>
    </nav>
{/if}
