{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($update)}
        <div class="alert alert-{if $update.is_latest}success{else}danger{/if}">
            <h4>
                {if $update.is_latest}
                    {lang t="system|acp3_up_to_date"}
                {else}
                    {lang t="system|acp3_not_up_to_date" args=['%link_start%' => "<a href=\"`$update.url`\" target=\"_blank\">",'%link_end%' => '</a>']}
                {/if}
            </h4>
            <ul class="list-unstyled">
                <li>
                    <strong>{lang t="system|installed_version"}:</strong>
                    {$update.installed_version}
                </li>
                <li>
                    <strong>{lang t="system|current_version"}:</strong>
                    {$update.latest_version}
                </li>
            </ul>
        </div>
    {else}
        <div class="alert alert-warning">
            <strong class="text-center">{lang t="system|error_update_check"}</strong>
        </div>
    {/if}
{/block}
