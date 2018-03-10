{include file="asset:System/Partials/form_group.input_text.tpl" name="share_description" value=$share.description maxlength=255 label={lang t="share|description"}}
{if !empty($share.uri_pattern)}
    <input type="hidden" name="share_uri_pattern" value="{$share.uri_pattern}">
{/if}
