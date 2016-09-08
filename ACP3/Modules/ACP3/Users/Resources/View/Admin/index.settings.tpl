{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120" required></div>
        </div>
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$languages required=true label={lang t="users|allow_language_override"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$entries required=true label={lang t="users|allow_entries_override"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$registration required=true label={lang t="users|enable_registration"}}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
