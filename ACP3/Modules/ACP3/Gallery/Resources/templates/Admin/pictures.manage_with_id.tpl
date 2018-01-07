{extends file="asset:Gallery/Admin/pictures.manage.tpl"}

{block GALLERY_PICTURE_UPLOAD}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="gallery|select_new_picture"}}
{/block}
