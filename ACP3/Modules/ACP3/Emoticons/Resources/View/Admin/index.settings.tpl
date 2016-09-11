{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="emoticons|image_width"} help={lang t="system|statements_in_pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="emoticons|image_height"} help={lang t="system|statements_in_pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="filesize" value=$form.filesize required=true label={lang t="emoticons|image_filesize"} help={lang t="system|statements_in_byte"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
{/block}
