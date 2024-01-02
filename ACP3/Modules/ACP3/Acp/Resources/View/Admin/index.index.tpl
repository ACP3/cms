{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <h4>{lang t="acp|access_to_modules"}</h4>
    <div class="row">
        {foreach $modules as $translatedModuleName => $internalModuleName}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <a href="{uri args="acp/`$internalModuleName`"}"
                   class="btn btn-light btn-lg d-flex align-items-center justify-content-center w-100 h-100">{$translatedModuleName}</a>
            </div>
        {/foreach}
    </div>
{/block}
