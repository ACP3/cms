{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="update|db_update_next_steps"}
    </p>
    <div class="alert alert-warning">
        {lang t="install|installation_successful_2"}
    </div>
    <dl class="dl-horizontal">
        {foreach $results as $module => $result}
            <dt>
                {lang t="$module|$module"}
            </dt>
            <dd>
                <span class="label label-{if $result === true}success{else}danger{/if}">
                    {if $result === true}
                        {lang t="update|db_update_success"}
                    {else}
                        {lang t="update|db_update_error"}
                    {/if}
                </span>
            </dd>
        {/foreach}
    </dl>
    <div class="well well-sm text-center">
        <a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="install|go_to_website"}</a>
    </div>
{/block}
