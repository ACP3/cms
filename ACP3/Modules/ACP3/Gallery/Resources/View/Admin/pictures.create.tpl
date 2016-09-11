{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {block GALLERY_PICTURE_UPLOAD}
        {include file="asset:System/Partials/form_group.input_file.tpl" name="file" required=true label={lang t="gallery|select_picture"}}
    {/block}
    <div class="form-group">
        <label for="description" class="col-sm-2 control-label">{lang t="system|description"}</label>
        <div class="col-sm-10">{wysiwyg name="description" value="`$form.description`" height="150" toolbar="simple"}</div>
    </div>
    {if isset($options)}
        {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/gallery/index/edit/id_`$gallery_id`"}}
{/block}
