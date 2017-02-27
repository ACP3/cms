{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="emoticons|image_width"}  input_group_after={lang t="system|pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="emoticons|image_height"}  input_group_after={lang t="system|pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="filesize" value=$form.filesize required=true label={lang t="emoticons|image_filesize"} input_group_after={lang t="system|byte"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
{/block}
