{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a href="#tab-contact" class="nav-link active" data-toggle="tab">{lang t="contact|contact"}</a></li>
            <li class="nav-item"><a href="#tab-legal" class="nav-link" data-toggle="tab">{lang t="contact|legal_statements"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-contact" class="tab-pane fade show active">
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="address" value=$form.address toolbar="simple" label={lang t="contact|address"}}
                {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail maxlength=120 label={lang t="system|email_address"}}
                {include file="asset:System/Partials/form_group.input_tel.tpl" name="telephone" value=$form.telephone maxlength=120 label={lang t="contact|telephone"}}
                {include file="asset:System/Partials/form_group.input_tel.tpl" name="mobile_phone" value=$form.mobile_phone maxlength=120 label={lang t="contact|mobile_phone"}}
                {include file="asset:System/Partials/form_group.input_tel.tpl" name="fax" value=$form.fax maxlength=120 label={lang t="contact|fax"}}
            </div>
            <div id="tab-legal" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="ceo" value=$form.ceo maxlength=120 label={lang t="contact|ceo"}}
                {include file="asset:System/Partials/form_group.input_text.tpl" name="vat_id" value=$form.vat_id maxlength=120 label={lang t="contact|vat_id"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="disclaimer" value=$form.disclaimer toolbar="simple" label={lang t="contact|disclaimer"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="picture_credits" value=$form.picture_credits toolbar="simple" label={lang t="contact|picture_credits"}}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/contact"}}
{/block}
