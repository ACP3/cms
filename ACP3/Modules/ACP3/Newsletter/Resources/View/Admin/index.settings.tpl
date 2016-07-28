{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>
            <div class="col-sm-10">
                <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120" required></div>
        </div>
        <div class="form-group">
            <label for="mailsig" class="col-sm-2 control-label">{lang t="newsletter|mailsig"}</label>
            <div class="col-sm-10">
                {wysiwyg name="mailsig" value="`$form.mailsig`" height="250"}
            </div>
        </div>
        <div class="form-group">
            <label for="{$html.0.id}" class="col-sm-2 control-label required">{lang t="newsletter|send_html_emails"}</label>
            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $html as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="html" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/newsletter"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
