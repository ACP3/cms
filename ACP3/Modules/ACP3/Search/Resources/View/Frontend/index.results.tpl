{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($results_mods)}
        {tabset identifier="search-results"}
            {foreach $results_mods as $module => $results}
                {tab title="{lang t="`$module`|`$module`"} <span class=\"badge bg-primary rounded-pill\">{count($results)}</span>"}
                    {foreach $results as $result}
                        <div class="card mb-3">
                            <div class="card-header">
                                <a href="{$result.hyperlink}">{$result.title}</a>
                            </div>
                            {if !empty($result.text)}
                                <div class="card-body">
                                    {$result.text|strip_tags|truncate:200}
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                {/tab}
            {/foreach}
        {/tabset}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="search|no_search_results" args=['%search_term%' => $search_term]}}
    {/if}
{/block}
