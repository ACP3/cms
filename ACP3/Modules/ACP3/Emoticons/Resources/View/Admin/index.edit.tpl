{extends file="asset:Emoticons/Admin/index.create.tpl"}

{block EMOTICONS_PICTURE_UPLOAD}
    <div class="form-group">
        <label for="picture" class="col-sm-2 control-label">{lang t="emoticons|replace_picture"}</label>

        <div class="col-sm-10"><input type="file" name="picture" id="picture"></div>
    </div>
{/block}