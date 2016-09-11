{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=20 readonly=$form.name_disabled label={lang t="system|name"}}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true readonly=$form.mail_disabled maxlength=120 label={lang t="system|email_address"}}
    <div class="form-group">
        <label for="message" class="col-sm-2 control-label required">{lang t="system|message"}</label>

        <div class="col-sm-10">
            <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$copy}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
{/block}
