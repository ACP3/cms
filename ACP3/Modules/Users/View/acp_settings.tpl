{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="form-group">
        <label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
    </div>
    <div class="form-group">
        <label for="{$languages.0.id}" class="col-lg-2 control-label">{lang t="users|allow_language_override"}</label>

        <div class="col-lg-10">
            <div class="btn-group" data-toggle="buttons">
                {foreach $languages as $row}
                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                        <input type="radio" name="language_override" id="{$row.id}" value="{$row.value}"{$row.checked}>
                        {$row.lang}
                    </label>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="{$entries.0.id}" class="col-lg-2 control-label">{lang t="users|allow_entries_override"}</label>

        <div class="col-lg-10">
            <div class="btn-group" data-toggle="buttons">
                {foreach $entries as $row}
                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                        <input type="radio" name="entries_override" id="{$row.id}" value="{$row.value}"{$row.checked}>
                        {$row.lang}
                    </label>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="{$registration.0.id}" class="col-lg-2 control-label">{lang t="users|enable_registration"}</label>

        <div class="col-lg-10">
            <div class="btn-group" data-toggle="buttons">
                {foreach $registration as $row}
                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                        <input type="radio" name="enable_registration" id="{$row.id}" value="{$row.value}"{$row.checked}>
                        {$row.lang}
                    </label>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/users"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>