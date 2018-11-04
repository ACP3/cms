{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {foreach $categories as $cat}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="h5 card-title mb-0">
                    <a href="{uri args="files/index/files/cat_`$cat.id`"}">{$cat.title}</a>
                </h3>
            </div>
            {if !empty($cat.description)}
                <div class="card-body">
                    {$cat.description}
                </div>
            {/if}
        </div>
    {foreachelse}
        {include file="asset:System/Partials/no_results.tpl"}
    {/foreach}
{/block}
