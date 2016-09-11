{extends file="asset:guestbook/ajax.tpl"}

{block CONTENT}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="modal-body">
            {if isset($error_msg)}
                {$error_msg}
            {/if}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true readonly=$form.name_disabled maxlength=20 label={lang t="system|name"}}
            {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail readonly=$form.mail_disabled maxlength=120 label={lang t="system|email_address"}}
            {include file="asset:System/Partials/form_group.input_url.tpl" name="website" value=$form.website readonly=$form.website_disabled maxlength=120 label={lang t="system|website"}}
            <div class="form-group">
                <label for="message" class="col-sm-2 control-label required">{lang t="system|message"}</label>

                <div class="col-sm-10">
                    {if $can_use_emoticons}
                        {event name="emoticons.render_emoticons_list"}
                    {/if}
                    <textarea class="form-control" name="message" id="message" cols="50" rows="6" required>{$form.message}</textarea>
                </div>
            </div>
            {if isset($subscribe_newsletter)}
                {include file="asset:System/Partials/form_group.checkbox.tpl" options=$subscribe_newsletter}
            {/if}
            {event name="captcha.event.display_captcha"}
        </div>
        <div class="modal-footer">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            {$form_token}
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
