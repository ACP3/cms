{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="card bg-light mb-3">
        <div class="card-body">
            <h4 class="card-title">{lang t="acp|access_to_modules"}</h4>
            <ul class="mb-0">
                {foreach $modules as $translatedModuleName => $internalModuleName}
                    <li><a href="{uri args="acp/`$internalModuleName`"}">{$translatedModuleName}</a></li>
                {/foreach}
            </ul>
        </div>
    </div>
{/block}
