{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal ajax-form">
    <div class="form-group">
        <label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
    </div>
    <div class="form-group">
        <label for="mailsig" class="col-lg-2 control-label">{lang t="newsletter|mailsig"}</label>

        <div class="col-lg-10">
            <textarea class="form-control" name="mailsig" id="mailsig" cols="50" rows="3">{$form.mailsig}</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/newsletter"}" class="btn btn-default">{lang t="system|cancel"}</a>
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