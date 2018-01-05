{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="update|db_update_next_steps"}
    </p>
    <div class="alert alert-warning">
        {lang t="install|installation_successful_2"}
    </div>
    <div class="collapse" id="modules-list">
        <dl class="dl-horizontal modules-list">
            {foreach $results as $module => $result}
                <dt>{$module}</dt>
                <dd>
                <span class="label label-{if $result === 1}success{elseif $result === 0}danger{else}info{/if}">
                    {if $result === 1}
                        {lang t="update|db_update_success"}
                    {elseif $result === 0}
                        {lang t="update|db_update_error"}
                    {else}
                        {lang t="update|db_update_no_update"}
                    {/if}
                </span>
                </dd>
            {/foreach}
        </dl>
    </div>
    <div class="well well-sm text-center">
        <a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="install|go_to_website"}</a>
        <a class="btn btn-default"
           role="button"
           data-toggle="collapse"
           href="#modules-list"
           aria-expanded="false"
           aria-controls="modules-list">
            {lang t="update|show_update_details"}
        </a>
    </div>
{/block}
