{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
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
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
