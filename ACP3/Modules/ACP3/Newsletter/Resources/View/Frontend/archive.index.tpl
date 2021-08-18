{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($newsletters)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        <div class="card mb-3">
            <div class="list-group list-group-flush">
                {foreach $newsletters as $row}
                    <a href="{uri args="newsletter/archive/details/id_`$row.id`"}"
                       class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                        <strong>{$row.title}</strong>
                        <time class="badge bg-primary rounded-pill" datetime="{date_format date=$row.date format="c"}">
                            {date_format date=$row.date format="short"}
                        </time>
                    </a>
                {/foreach}
            </div>
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
