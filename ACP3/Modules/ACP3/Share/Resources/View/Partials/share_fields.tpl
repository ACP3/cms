{include file="asset:System/Partials/form_group.button_group.tpl" options=$share.active required=true label={lang t="share|activate_sharing"}}
{include file="asset:System/Partials/form_group.button_group.tpl" options=$share.ratings_active required=true label={lang t="share|activate_ratings"}}
{if !empty($share.uri_pattern)}
    <input type="hidden" name="share_uri_pattern" value="{$share.uri_pattern}">
{/if}
