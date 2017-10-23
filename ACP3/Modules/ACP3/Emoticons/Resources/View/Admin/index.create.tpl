{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="code" value=$form.code required=true maxlength=10 label={lang t="emoticons|code"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description required=true maxlength=15 label={lang t="system|description"}}
    {block EMOTICONS_PICTURE_UPLOAD}
        {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" required=true label={lang t="emoticons|picture"}}
    {/block}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
{/block}
