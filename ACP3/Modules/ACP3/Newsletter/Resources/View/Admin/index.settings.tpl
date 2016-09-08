{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
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
