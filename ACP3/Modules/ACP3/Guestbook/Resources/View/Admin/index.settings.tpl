{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
        {include file="asset:System/Partials/form_group.select.tpl" options=$notify required=true label={lang t="guestbook|notification"}}
        <div class="form-group">
            <label for="notify-email" class="col-sm-2 control-label required">{lang t="guestbook|notification_email"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="notify_email" id="notify-email" value="{$form.notify_email}" required>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$overlay required=true label={lang t="guestbook|use_overlay"}}
        {if isset($allow_emoticons)}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$allow_emoticons required=true label={lang t="guestbook|allow_emoticons"}}
        {/if}
        {if isset($newsletter_integration)}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$newsletter_integration required=true label={lang t="guestbook|newsletter_integration"}}
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/guestbook"}}
    </form>
    {javascripts}
        {include_js module="guestbook" file="admin/index.settings"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
