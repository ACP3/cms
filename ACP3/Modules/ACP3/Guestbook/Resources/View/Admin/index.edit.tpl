{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="message" class="col-sm-2 control-label required">{lang t="system|message"}</label>

            <div class="col-sm-10">
                {if $can_use_emoticons}
                    {event name="emoticons.render_emoticons_list"}
                {/if}
                <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
            </div>
        </div>
        {if !empty($activate)}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$activate required=true label={lang t="guestbook|activate_entry"}}
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/guestbook"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
