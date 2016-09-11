{extends file="asset:Emoticons/Admin/index.create.tpl"}

{block EMOTICONS_PICTURE_UPLOAD}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="emoticons|replace_picture"}}
{/block}
