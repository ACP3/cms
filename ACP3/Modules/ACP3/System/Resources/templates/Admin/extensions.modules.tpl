{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-9 cache-type">
                {lang t="system|refresh_caches"}
            </div>
            <div class="col-sm-3">
                <a href="{uri args="acp/system/extensions/modules/action_refresh"}"
                   class="btn btn-block btn-default"
                   data-ajax-form="true"
                   data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                    <i class="fa fa-refresh"></i>
                    {lang t="system|refresh"}
                </a>
            </div>
        </div>
    </div>
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|installed_modules"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|installable_modules"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/datagrid.modules.tpl" modules=$installed_modules}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {if !empty($new_modules)}
                    {include file="asset:System/Partials/datagrid.modules.tpl" modules=$new_modules}
                {else}
                    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|no_modules_available_for_installation"}}
                {/if}
            </div>
        </div>
    </div>
    {javascripts}
        {include_js module="system" file="ajax-form"}
        {include_js module="system" file="hash-change"}
    {/javascripts}
{/block}
