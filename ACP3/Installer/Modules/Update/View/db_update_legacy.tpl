{if isset($results)}
    <ul>
        {foreach $results as $row}
            <li>
                {$row.text}
                <span class="label label-{$row.class}">{$row.result_text}</span>
            </li>
        {/foreach}
    </ul>
    <form action="{uri args="install/db_update"}" method="post">
        <div class="well well-sm text-center">
            <button type="submit" name="update" class="btn btn-primary">{lang t="forward"}</button>
        </div>
    </form>
{else}
    <p>{lang t="legacy_db_update_description"}</p>
    <form action="{$REQUEST_URI}" method="post">
        <div class="well well-sm text-center">
            <button type="submit" name="update" class="btn btn-primary">{lang t="do_db_update"}</button>
        </div>
    </form>
{/if}