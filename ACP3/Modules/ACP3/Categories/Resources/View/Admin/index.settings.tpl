{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="categories|image_width"} help={lang t="system|statements_in_pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="categories|image_height"} help={lang t="system|statements_in_pixel"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="filesize" value=$form.filesize required=true label={lang t="categories|image_filesize"} help={lang t="system|statements_in_byte"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/categories"}}
{/block}
