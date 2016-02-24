{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="salutation" class="col-sm-2 control-label">{lang t="newsletter|salutation"}</label>
            <div class="col-sm-10">
                <select class="form-control" name="salutation" id="salutation">
                    <option value=""></option>
                    {foreach $salutation as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-2 control-label">{lang t="newsletter|first_name"}</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" name="first_name" id="first_name" maxlength="120" value="{$form.first_name}">
            </div>
        </div>
        <div class="form-group">
            <label for="last_name" class="col-sm-2 control-label">{lang t="newsletter|last_name"}</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" name="last_name" id="last_name" maxlength="120" value="{$form.last_name}">
            </div>
        </div>
        <div class="form-group">
            <label for="mail" class="col-sm-2 control-label required">{lang t="system|email_address"}</label>
            <div class="col-sm-10">
                <input class="form-control" type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required>
            </div>
        </div>
        {event name="captcha.event.display_captcha"}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="newsletter/archive/index"}" class="btn btn-link">{lang t="newsletter|missed_out_newsletter"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}