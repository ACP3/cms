{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    {redirect_message}
    <form action="{uri args="acp/contact"}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">{lang t="contact|address"}</label>

            <div class="col-sm-10">
                {wysiwyg name="address" value="`$form.address`" toolbar="simple"}
            </div>
        </div>
        {include file="asset:System/Partials/form_group.input_text.tpl" name="ceo" value=$form.ceo maxlength=120 label={lang t="contact|ceo"}}
        {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail maxlength=120 label={lang t="system|email_address"}}
        {include file="asset:System/Partials/form_group.input_tel.tpl" name="telephone" value=$form.telephone maxlength=120 label={lang t="contact|telephone"}}
        {include file="asset:System/Partials/form_group.input_tel.tpl" name="fax" value=$form.fax maxlength=120 label={lang t="contact|fax"}}
        {include file="asset:System/Partials/form_group.input_text.tpl" name="vat_id" value=$form.vat_id maxlength=120 label={lang t="contact|vat_id"}}
        <div class="form-group">
            <label for="disclaimer" class="col-sm-2 control-label">{lang t="contact|disclaimer"}</label>

            <div class="col-sm-10">
                {wysiwyg name="disclaimer" value="`$form.disclaimer`" toolbar="simple"}
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/contact"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
