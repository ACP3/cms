{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <h4>{lang t="acp|access_to_modules"}</h4>
    <div class="row">
        {foreach $modules as $translatedModuleName => $internalModuleName}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="card bg-body-tertiary position-relative h-100">
                    <div class="card-body text-center">
                        <a href="{uri args="acp/`$internalModuleName`"}" class="stretched-link">{$translatedModuleName}</a>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{/block}
