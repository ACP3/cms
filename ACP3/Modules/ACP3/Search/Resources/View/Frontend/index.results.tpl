{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($results_mods)}
        {tabset identifier="search-results"}
            {foreach $results_mods as $module => $results}
                {tab title="{lang t="`$module`|`$module`"} <span class=\"badge\">{count($results)}</span>"}
                    {foreach $results as $result}
                        <div class="dataset-box">
                            <header class="navbar navbar-default">
                                <div class="navbar-header">
                                    <h3 class="navbar-brand">
                                        <a href="{$result.hyperlink}">{$result.title}</a>
                                    </h3>
                                </div>
                            </header>
                            {if !empty($result.text)}
                                <div class="content">
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
