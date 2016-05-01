{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="nick-mail" class="col-sm-2 control-label required">{lang t="users|nickname_or_email"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="nick_mail" id="nick-mail" value="{$form.nick_mail}" maxlength="120" required>

                <p class="help-block">{lang t="users|forgot_pwd_description"}</p>
            </div>
        </div>
        {event name="captcha.event.display_captcha"}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}
