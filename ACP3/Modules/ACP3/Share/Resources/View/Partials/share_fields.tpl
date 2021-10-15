{if !empty($share.services)}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$share.active required=true label={lang t="share|activate_sharing"}}
    <div id="share-services-wrapper">
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$share.customize_services required=true label={lang t="share|customize_services"}}
        {include file="asset:System/Partials/form_group.select.tpl" options=$share.services multiple=true required=true formGroupId="share-custom-services-wrapper" label={lang t="share|active_services"}}
    </div>
{/if}
{include file="asset:System/Partials/form_group.button_group.tpl" options=$share.ratings_active required=true label={lang t="share|activate_ratings"}}
{if !empty($share.uri_pattern)}
    <input type="hidden" name="share_uri_pattern" value="{$share.uri_pattern}">
{/if}
{javascripts}
    {include_js module="share" file="partials/share_fields"}
{/javascripts}
