{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <p>
        {lang t="system|delete_caches_description"}
    </p>
    {foreach $cache_types as $cache_type}
        <div class="well well-sm">
            <div class="row">
                <div class="col-sm-9 cache-type">
                    {lang t="system|cache_type_`$cache_type`"}
                </div>
                <div class="col-sm-3">
                    <a href="{uri args="acp/system/maintenance/cache/action_`$cache_type`"}"
                       class="btn btn-block btn-warning"
                       data-ajax-form="true"
                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                        {icon iconSet="solid" icon="sync-alt"}
                        {lang t="system|refresh"}
                    </a>
                </div>
            </div>
        </div>
    {/foreach}
    {js_libraries enable="ajax-form"}
{/block}
