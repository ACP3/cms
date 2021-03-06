{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="well well-sm">
        <h4>{lang t="acp|access_to_modules"}</h4>
        <ul>
            {foreach $modules as $translatedModuleName => $internalModuleName}
                <li><a href="{uri args="acp/`$internalModuleName`"}">{$translatedModuleName}</a></li>
            {/foreach}
        </ul>
    </div>
{/block}
