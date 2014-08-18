{if isset($results)}
    <ul>
        {foreach $results as $row}
            <li>
                {$row.text}
                <span class="label label-{$row.class}">{$row.result_text}</span>
            </li>
        {/foreach}
    </ul>
    <form action="{uri args="update"}" method="post">
        <div class="well well-sm text-center">
            <button type="submit" name="update" class="btn btn-primary">{lang t="install|forward"}</button>
        </div>
    </form>
{else}
    <p>{lang t="update|legacy_db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post" data-ajax-form="true" data-ajax-form-loading-text="{lang t="install|loading_please_wait"}">
        <div class="well well-sm text-center">
            <input type="hidden" name="action" value="confirmed">
            <button type="submit" name="update" class="btn btn-primary">{lang t="update|do_db_update"}</button>
        </div>
    </form>
    {include_js module="system" file="forms"}
{/if}