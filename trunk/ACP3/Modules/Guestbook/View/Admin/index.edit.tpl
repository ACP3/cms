{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">{lang t="system|name"}</label>

        <div class="col-sm-10">
            <input class="form-control" type="text" name="name" id="name" value="{$form.name}" required></div>
    </div>
    <div class="form-group">
        <label for="message" class="col-sm-2 control-label">{lang t="system|message"}</label>

        <div class="col-sm-10">
            {if isset($emoticons)}{$emoticons}{/if}
            <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
        </div>
    </div>
    {if isset($activate)}
        <div class="form-group">
            <label for="active-1" class="col-sm-2 control-label">{lang t="guestbook|activate_entry"}</label>

            <div class="col-sm-10">
                {foreach $activate as $row}
                    <div class="checkbox">
                        <label for="active-{$row.value}">
                            <input type="radio" name="active" id="active-{$row.value}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/guestbook"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}