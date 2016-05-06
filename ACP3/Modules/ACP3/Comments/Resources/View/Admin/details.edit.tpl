{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {if !isset($form.user_id)}
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label required">{lang t="system|name"}</label>

                <div class="col-sm-10">
                    <input class="form-control" type="text" name="name" id="name" value="{$form.name}" required>
                </div>
            </div>
        {/if}
        <div class="form-group">
            <label for="message" class="col-sm-2 control-label required">{lang t="system|message"}</label>

            <div class="col-sm-10">
                {if isset($emoticons)}{$emoticons}{/if}
                <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                {if isset($form.user_id) && $form.user_id != '0'}
                    <input type="hidden" name="user_id" value="{$form.user_id}">
                {/if}
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/comments/details/index/id_`$module_id`"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
