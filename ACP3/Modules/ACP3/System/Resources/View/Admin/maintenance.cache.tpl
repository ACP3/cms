{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <p>
        {lang t="system|delete_caches_description"}
    </p>
    <div class="card mb-3">
        <div class="list-group list-group-flush">
            {foreach $cache_types as $cache_type}
                <div class="list-group-item bg-light">
                    <div class="row">
                        <div class="col-sm-9 cache-type">
                            {lang t="system|cache_type_`$cache_type`"}
                        </div>
                        <div class="col-sm-3">
                            <a href="{uri args="acp/system/maintenance/cache/action_`$cache_type`"}"
                               class="btn btn-warning d-block"
                               data-ajax-form="true"
                               data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                {icon iconSet="solid" icon="rotate"}
                                {lang t="system|refresh"}
                            </a>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {js_libraries enable="ajax-form"}
{/block}
