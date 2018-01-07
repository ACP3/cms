{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($newsletters)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $newsletters as $row}
            <div class="dataset-box">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <h2 class="navbar-brand">
                            <a href="{uri args="newsletter/archive/details/id_`$row.id`"}">{$row.title}</a>
                        </h2>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.date format="c"}">
                        {date_format date=$row.date format="short"}
                    </time>
                </div>
            </div>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}