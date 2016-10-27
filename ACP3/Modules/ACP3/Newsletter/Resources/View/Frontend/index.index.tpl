{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$salutation columnSelector="col-sm-3" label={lang t="newsletter|salutation"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="first_name" value=$form.first_name maxlength=120 label={lang t="newsletter|first_name"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="last_name" value=$form.last_name maxlength=120 label={lang t="newsletter|last_name"}}
    {event name="captcha.event.display_captcha"}
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="newsletter/archive/index"}" class="btn btn-link">{lang t="newsletter|missed_out_newsletter"}</a>
            {$form_token}
        </div>
    </div>
{/block}
