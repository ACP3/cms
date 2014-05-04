{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal " data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="form-group">
        <label for="nick-mail" class="col-lg-2 control-label">{lang t="users|nickname_or_email"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="nick_mail" id="nick-mail" value="{$form.nick_mail}" maxlength="120" required>

            <p class="help-block">{lang t="users|forgot_pwd_description"}</p>
        </div>
    </div>
    {if isset($captcha)}
        {$captcha}
    {/if}
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}