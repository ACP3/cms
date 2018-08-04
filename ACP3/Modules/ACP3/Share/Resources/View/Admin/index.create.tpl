{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="uri" value=$form.uri required=true maxlength=191 label={lang t="share|uri"}}
    {include file="asset:Share/Partials/share_fields.tpl" share=$SHARE_FORM_FIELDS}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/share"}}
{/block}
