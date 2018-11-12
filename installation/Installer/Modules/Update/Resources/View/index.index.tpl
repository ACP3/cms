{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>{lang t="update|db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post" data-ajax-form="true" data-ajax-form-loading-text="{lang t="install|loading_please_wait"}">
        <div class="card bg-light">
            <div class="card-body text-center p-2">
                <input type="hidden" name="action" value="confirmed">
                <button type="submit" name="update" class="btn btn-primary">{lang t="update|do_db_update"}</button>
            </div>
        </div>
    </form>
    {javascripts}
        <script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/ajax-form.js"></script>
    {/javascripts}
{/block}
