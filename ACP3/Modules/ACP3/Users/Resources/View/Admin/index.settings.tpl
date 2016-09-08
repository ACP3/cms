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
            <label for="{$languages.0.id}" class="col-sm-2 control-label required">{lang t="users|allow_language_override"}</label>

            <div class="col-sm-10">
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
            <label for="{$entries.0.id}" class="col-sm-2 control-label required">{lang t="users|allow_entries_override"}</label>

            <div class="col-sm-10">
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
            <label for="{$registration.0.id}" class="col-sm-2 control-label required">{lang t="users|enable_registration"}</label>

            <div class="col-sm-10">
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
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
