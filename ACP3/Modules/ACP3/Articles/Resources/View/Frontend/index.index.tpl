{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($articles)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $articles as $row}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-end">
                    <h3 class="h5 card-title flex-grow-1 mb-0">
                        <a href="{uri args="articles/index/details/id_`$row.id`"}">{$row.title}</a>
                    </h3>
                    <time class="card-subtitle small" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start}
                    </time>
                </div>
            </div>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
