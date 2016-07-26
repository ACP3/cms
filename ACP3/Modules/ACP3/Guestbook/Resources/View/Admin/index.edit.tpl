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
            <div class="form-group">
                <label for="active-1" class="col-sm-2 control-label required">{lang t="guestbook|activate_entry"}</label>

                <div class="col-sm-10">
                    <div class="btn-group" data-toggle="buttons">
                        {foreach $activate as $row}
                            <label for="active-{$row.value}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                <input type="radio" name="active" id="active-{$row.value}" value="{$row.value}"{$row.checked}>
                                {$row.lang}
                            </label>
                        {/foreach}
                    </div>
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
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
