{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>{lang t="installer|db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post" data-ajax-form="true" data-ajax-form-loading-text="{lang t="installer|loading_please_wait"}">
        <div class="well well-sm text-center">
            <input type="hidden" name="action" value="confirmed">
            <button type="submit" name="update" class="btn btn-primary">{lang t="installer|do_db_update"}</button>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="partials/ajax-form"}
    {/javascripts}
{/block}
