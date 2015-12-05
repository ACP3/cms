{extends file="asset:Users/Admin/index.create.tpl"}

{block PASSWORD_FIELDS}
    <div class="form-group">
        <label for="new-pwd" class="col-sm-2 control-label">{lang t="users|new_pwd"}</label>
        <div class="col-sm-10"><input class="form-control" type="password" name="new_pwd" id="new-pwd"></div>
    </div>
    <div class="form-group">
        <label for="new-pwd-repeat" class="col-sm-2 control-label">{lang t="users|new_pwd_repeat"}</label>
        <div class="col-sm-10"><input class="form-control" type="password" name="new_pwd_repeat" id="new-pwd-repeat"></div>
    </div>
{/block}