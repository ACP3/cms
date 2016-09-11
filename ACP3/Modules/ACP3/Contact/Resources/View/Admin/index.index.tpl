{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="address" value=$form.address toolbar="simple" label={lang t="contact|address"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="ceo" value=$form.ceo maxlength=120 label={lang t="contact|ceo"}}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.input_tel.tpl" name="telephone" value=$form.telephone maxlength=120 label={lang t="contact|telephone"}}
    {include file="asset:System/Partials/form_group.input_tel.tpl" name="fax" value=$form.fax maxlength=120 label={lang t="contact|fax"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="vat_id" value=$form.vat_id maxlength=120 label={lang t="contact|vat_id"}}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="disclaimer" value=$form.disclaimer toolbar="simple" label={lang t="contact|disclaimer"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/contact"}}
{/block}
