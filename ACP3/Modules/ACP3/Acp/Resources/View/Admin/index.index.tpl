{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="well well-sm">
        <h4>{lang t="acp|access_to_modules"}</h4>
        <ul>
            {foreach $modules as $module}
                <li><a href="{uri args="acp/`$module.dir`"}">{lang t="`$module.name`|`$module.name`"}</a></li>
            {/foreach}
        </ul>
    </div>
{/block}
