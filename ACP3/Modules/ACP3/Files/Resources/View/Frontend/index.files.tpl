{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($categories)}
        {include file="asset:Files/Partials/categories_list.tpl" categories=$categories title={lang t="files|further_categories"}}
        <hr>
    {/if}
    {if !empty($files)}
        <div class="card mb-3">
            <div class="list-group list-group-flush">
                {foreach $files as $row}
                    <a href="{uri args="files/index/details/id_`$row.id`"}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        {$row.title}
                        {if !empty($row.size)}
                            ({$row.size})
                        {else}
                            ({lang t="files|unknown_filesize"})
                        {/if}
                        <time class="badge bg-primary rounded-pill" datetime="{date_format date=$row.start format="c"}">{date_format date=$row.start format=$dateformat}</time>
                    </a>
                {/foreach}
            </div>
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
