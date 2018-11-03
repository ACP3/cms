{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($results_mods)}
        <ul class="nav nav-tabs mb-3">
            {$i=0}
            {foreach $results_mods as $module => $values}
                <li class="nav-item">
                    <a href="#tab-{$module}" class="nav-link{if $values@first} active{/if}" data-toggle="tab">
                        {lang t="`$module`|`$module`"}
                        <span class="badge">{count($values)}</span>
                    </a>
                </li>
            {/foreach}
        </ul>
        <div class="tab-content">
            {foreach $results_mods as $module => $results}
                <div id="tab-{$module}" class="tab-pane fade{if $results@first} in active{/if}">
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
                </div>
            {/foreach}
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="search|no_search_results" args=['%search_term%' => $search_term]}}
    {/if}
{/block}
