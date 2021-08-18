{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>{lang t="installer|db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post" data-ajax-form="true" data-ajax-form-loading-text="{lang t="installer|loading_please_wait"}">
        <div class="card bg-light mb-3">
            <div class="card-body text-center">
                <input type="hidden" name="action" value="confirmed">
                <button type="submit" name="update" class="btn btn-primary">{lang t="installer|do_db_update"}</button>
            </div>
        </div>
    </form>
    {javascripts}
        {js_libraries enable="ajax-form"}
    {/javascripts}
{/block}
