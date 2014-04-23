{if isset($error_msg)}
    {$error_msg}
{/if}
{if isset($redirect_message)}
    {$redirect_message}
{/if}
<form action="{uri args="acp/contact"}" method="post" accept-charset="UTF-8" class="form-horizontal ajax-form">
    <div class="form-group">
        <label for="address" class="col-lg-2 control-label">{lang t="contact|address"}</label>

        <div class="col-lg-10">{wysiwyg name="address" value="`$form.address`" toolbar="simple"}</div>
    </div>
    <div class="form-group">
        <label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
    </div>
    <div class="form-group">
        <label for="telephone" class="col-lg-2 control-label">{lang t="contact|telephone"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="tel" name="telephone" id="telephone" value="{$form.telephone}" maxlength="120">
        </div>
    </div>
    <div class="form-group">
        <label for="fax" class="col-lg-2 control-label">{lang t="contact|fax"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="tel" name="fax" id="fax" value="{$form.fax}" maxlength="120"></div>
    </div>
    <div class="form-group">
        <label for="disclaimer" class="col-lg-2 control-label">{lang t="contact|disclaimer"}</label>

        <div class="col-lg-10">{wysiwyg name="disclaimer" value="`$form.disclaimer`" toolbar="simple"}</div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/contact"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.ajax-form').formSubmit('{lang t="system|loading_please_wait"}');
    });
</script>