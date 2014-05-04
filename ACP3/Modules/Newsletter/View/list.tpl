{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal " data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="form-group">
        <label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required><br>
            {foreach $actions as $row}
                <label for="{$row.id}" class="radio-inline">
                    <input type="radio" name="action" id="{$row.id}" value="{$row.value}"{$row.checked}>
                    {$row.lang}
                </label>
            {/foreach}
        </div>
    </div>
    {if isset($captcha)}
        {$captcha}
    {/if}
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="newsletter/list_archive"}" class="btn btn-link">{lang t="newsletter|missed_out_newsletter"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}