{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($categories)}
        {include file="asset:Files/Partials/categories_list.tpl" categories=$categories title={lang t="files|category_select"}}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
