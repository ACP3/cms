{extends file="asset:`$LAYOUT`"}

{block CONTENT prepend}
    {if isset($results)}
        <ul>
            {foreach $results as $row}
                <li>
                    {$row.text}
                    <span class="label label-{$row.class}">{$row.result_text}</span>
                </li>
            {/foreach}
        </ul>
        <p>
            {lang t="update|db_update_next_steps"}
        </p>
        <div class="alert alert-warning">
            {lang t="install|installation_successful_2"}
        </div>
        <div class="well well-sm text-center">
            <a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="install|go_to_website"}</a>
        </div>
    {else}
        <p>{lang t="update|db_update_description"}</p>
        <form action="{$REQUEST_URI}" method="post" data-ajax-form="true" data-ajax-form-loading-text="{lang t="install|loading_please_wait"}">
            <div class="well well-sm text-center">
                <input type="hidden" name="action" value="confirmed">
                <button type="submit" name="update" class="btn btn-primary">{lang t="update|do_db_update"}</button>
            </div>
        </form>
        {javascripts}
            <script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/forms.js"></script>
        {/javascripts}
    {/if}
{/block}
