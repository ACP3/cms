{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="installer|db_update_next_steps"}
    </p>
    {include file="asset:Installer/Partials/delete_installer_directory.tpl"}
    <dl class="dl-horizontal">
        {foreach $results as $module => $result}
            <dt>
                {lang t="$module|$module"}
            </dt>
            <dd>
                <span class="label label-{if $result === true}success{else}danger{/if}">
                    {if $result === true}
                        {lang t="installer|db_update_success"}
                    {else}
                        {lang t="installer|db_update_error"}
                    {/if}
                </span>
            </dd>
        {/foreach}
    </dl>
    {include file="asset:Installer/Partials/next_steps.tpl"}
{/block}
