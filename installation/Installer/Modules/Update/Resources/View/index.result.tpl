{extends file="asset:`$LAYOUT`"}

{block CONTENT prepend}
    <ul>
        {foreach $results as $module => $result}
            <li>
                {lang t="update|db_update_text" args=['%module%' => $module]}
                <span class="label label-{if $result === 1}success{elseif $result === 0}danger{else}info{/if}">
                    {if $result === 1}
                        {lang t="update|db_update_success"}
                    {elseif $result === 0}
                        {lang t="update|db_update_error"}
                    {else}
                        {lang t="update|db_update_no_update"}
                    {/if}
                </span>
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
{/block}
