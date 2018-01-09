{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {block GALLERY_PICTURE_UPLOAD}
        {include file="asset:System/Partials/form_group.input_file.tpl" name="file" required=true label={lang t="gallery|select_picture"}}
    {/block}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="description" value=$form.description toolbar="simple" label={lang t="system|description"}}
    {if !empty($options)}
        {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/gallery/pictures/index/id_`$gallery_id`"}}
{/block}
