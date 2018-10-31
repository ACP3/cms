{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="card">
        <div class="card-header">{lang t="acp|access_to_modules"}</div>
        <div class="card-body">
            <ul class="mb-0">
                {foreach $modules as $module}
                    <li><a href="{uri args="acp/`$module.dir`"}">{lang t="`$module.name`|`$module.name`"}</a></li>
                {/foreach}
            </ul>
        </div>
    </div>
{/block}
