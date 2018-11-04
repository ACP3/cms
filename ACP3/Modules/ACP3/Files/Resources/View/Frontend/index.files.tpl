{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {include file="asset:Files/Partials/categories_list.tpl" categories=$categories title={lang t="files|further_categories"}}
    {if !empty($files)}
        {foreach $files as $row}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-end">
                    <h3 class="h5 card-title flex-grow-1 mb-0">
                        <a href="{uri args="files/index/details/id_`$row.id`"}">
                            {$row.title}
                            {if !empty($row.size)}
                                ({$row.size})
                            {else}
                                ({lang t="files|unknown_filesize"})
                            {/if}
                        </a>
                    </h3>
                    <time class="card-subtitle small" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start format=$dateformat}
                    </time>
                </div>
            </div>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
