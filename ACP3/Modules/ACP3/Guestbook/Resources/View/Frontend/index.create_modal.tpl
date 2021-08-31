{$LAYOUT='Guestbook/layout.modal.tpl'}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="modal-body">
        {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true readonly=$form.name_disabled maxlength=20 label={lang t="system|name"}}
        {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail readonly=$form.mail_disabled maxlength=120 label={lang t="system|email_address"}}
        {include file="asset:System/Partials/form_group.input_url.tpl" name="website" value=$form.website readonly=$form.website_disabled maxlength=120 label={lang t="system|website"}}
        {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="message" value=$form.message required=true label={lang t="system|message"} editor="core.wysiwyg.textarea"}
        {event name="guestbook.layout.create"}
        {event name="captcha.event.display_captcha"}
    </div>
    <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
        {$form_token}
    </div>
{/block}
