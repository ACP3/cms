{if !empty($pagination)}
    <nav class="text-center" aria-label="{lang t="system|pagination"}">
        <ul class="pagination">
            {foreach $pagination as $row}
                <li{if $row.selected} class="active"{/if}>
                    <a href="{$row.uri}"
                       {if !empty($row.selector)} class="{$row.selector}"{/if}
                       {if !empty($row.title)} title="{$row.title}" aria-label="{$row.title}"{/if}>{$row.page}</a>
                </li>
            {/foreach}
        </ul>
    </nav>
{/if}
