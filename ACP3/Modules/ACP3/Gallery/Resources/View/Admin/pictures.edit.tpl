{extends file="asset:Gallery/Admin/pictures.create.tpl"}

{block GALLERY_PICTURE_UPLOAD}
    {include file="asset:System/Partials/form_group.select.tpl" options=$galleries required=true label={lang t="gallery|move_to_gallery"}}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="gallery|select_new_picture"}}
{/block}
