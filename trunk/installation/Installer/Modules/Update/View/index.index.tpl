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
        {lang t="update|installation_successful_2"}
    </div>
    <div class="well well-sm text-center">
        <a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="update|go_to_website"}</a>
    </div>
{else}
    <p>{lang t="update|db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post">
        <div class="well well-sm text-center">
            <button type="submit" name="update" class="btn btn-primary">{lang t="update|do_db_update"}</button>
        </div>
    </form>
{/if}