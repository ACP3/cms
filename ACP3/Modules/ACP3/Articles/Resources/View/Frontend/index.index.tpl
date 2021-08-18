{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($articles)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        <div class="card">
            <div class="list-group list-group-flush">
            {foreach $articles as $row}
                <a href="{uri args="articles/index/details/id_`$row.id`"}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    {$row.title}
                    <time class="badge bg-primary rounded-pill" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start}
                    </time>
                </a>
            {/foreach}
            </div>
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
