{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($update)}
        <div class="alert alert-{if $update.is_latest}success{else}danger{/if}">
            <h5 class="alert-heading">
                {if $update.is_latest}
                    {lang t="system|acp3_up_to_date"}
                {else}
                    {lang t="system|acp3_not_up_to_date" args=['%link_start%' => "<a href=\"`$update.url`\" target=\"_blank\">",'%link_end%' => '</a>']}
                {/if}
            </h5>
            <hr>
            <dl class="row mb-0">
                <dt class="col-sm-5 col-md-3 col-xl-2">{lang t="system|installed_version"}:</dt>
                <dd class="col-sm-7 col-md-9 col-xl-10">{$update.installed_version}</dd>
                <dt class="col-sm-5 col-md-3 col-xl-2">{lang t="system|current_version"}:</dt>
                <dd class="col-sm-7 col-md-9 col-xl-10">{$update.latest_version}</dd>
            </dl>
        </div>
    {else}
        <div class="alert alert-warning text-center">
            {lang t="system|error_update_check"}
        </div>
    {/if}
{/block}
