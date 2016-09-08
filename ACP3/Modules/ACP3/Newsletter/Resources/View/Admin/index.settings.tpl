{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>
            <div class="col-sm-10">
                <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120" required>
            </div>
        </div>
        <div class="form-group">
            <label for="mailsig" class="col-sm-2 control-label">{lang t="newsletter|mailsig"}</label>
            <div class="col-sm-10">
                {wysiwyg name="mailsig" value="`$form.mailsig`" height="250"}
            </div>
        </div>
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$html required=true label={lang t="newsletter|send_html_emails"}}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/newsletter"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
