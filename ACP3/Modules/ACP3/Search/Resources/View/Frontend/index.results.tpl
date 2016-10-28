{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($results_mods)}
        <div class="tabbable">
            <ul class="nav nav-tabs">
                {$i=0}
                {foreach $results_mods as $module => $values}
                    <li{if $values@first} class="active"{/if}>
                        <a href="#tab-{$values.dir}" data-toggle="tab">
                            {lang t="`$module`|`$module`"}
                        </a>
                    </li>
                {/foreach}
            </ul>
            <div class="tab-content">
                {foreach $results_mods as $module => $values}
                    <div id="tab-{$values.dir}" class="tab-pane fade{if $values@first} in active{/if}">
                        {foreach $values.results as $row}
                            <div class="dataset-box">
                                <header class="navbar navbar-default">
                                    <div class="navbar-header">
                                        <h2 class="navbar-brand"><a href="{$row.hyperlink}">{$row.title}</a></h2>
                                    </div>
                                </header>
                                {if !empty($row.text)}
                                    <div class="content">
                                        {$row.text|strip_tags|truncate:200}
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
                {/foreach}
            </div>
        </div>
    {else}
        <div class="alert alert-warning text-center">
            <strong>{$no_search_results}</strong>
        </div>
    {/if}
{/block}
