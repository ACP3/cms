{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label required">{lang t="users|nickname"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30" required>
            </div>
        </div>
        <div class="form-group">
            <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120" required>
            </div>
        </div>
        {include file="asset:Users/Partials/password_fields.tpl" required=true}
        {event name="captcha.event.display_captcha"}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
