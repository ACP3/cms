<div class="text-center">
    <ul class="pagination">
        {foreach $pagination as $row}
            <li{if $row.selected} class="active"{/if}>
                <a href="{$row.uri}"{if isset($row.title)} title="{$row.title}"{/if}>{$row.page}</a>
            </li>
        {/foreach}
    </ul>
</div>
