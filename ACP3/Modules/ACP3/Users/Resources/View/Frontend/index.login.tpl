{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="nickname" required=true maxlength=30 label={lang t="users|nickname"}}
    {include file="asset:System/Partials/form_group.input_password.tpl" name="pwd" required=true label={lang t="users|pwd"}}
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$remember_me}
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">
                <i class="glyphicon glyphicon-lock"></i>
                {lang t="users|log_in"}
            </button>
            <a href="{uri args="users/index/forgot_pwd"}" class="btn btn-link">{lang t="users|forgot_pwd"}</a>
        </div>
    </div>
{/block}
