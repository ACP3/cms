<br/>{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8" class="form-horizontal ajax-form">
    <div class="form-group">
        <label for="name" class="col-lg-2 control-label">{lang t="system|name"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}>
        </div>
    </div>
    <div class="form-group">
        <label for="message" class="col-lg-2 control-label">{lang t="system|message"}</label>

        <div class="col-lg-10">
            {if isset($emoticons)}{$emoticons}{/if}
            <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
        </div>
    </div>
    {if isset($captcha)}
        {$captcha}
    {/if}
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
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