{extends file="asset:Gallery/Admin/pictures.create.tpl"}

{block GALLERY_PICTURE_UPLOAD}
    <div class="form-group">
        <label for="file" class="col-sm-2 control-label">{lang t="gallery|select_new_picture"}</label>
        <div class="col-sm-10"><input type="file" name="file" id="file"></div>
    </div>
{/block}