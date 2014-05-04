{if isset($error_msg)}
    {$error_msg}
{/if}
{if isset($redirect_message)}
    {$redirect_message}
{/if}
<form action="{uri args="acp/feeds"}" method="post" accept-charset="UTF-8" class="form-horizontal " data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="form-group">
        <label for="feed-image" class="col-lg-2 control-label">{lang t="feeds|feed_image"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="feed_image" id="feed-image" value="{$form.feed_image}" maxlength="120">
        </div>
    </div>
    <div class="form-group">
        <label for="feed-type" class="col-lg-2 control-label">{lang t="feeds|feed_type"}</label>

        <div class="col-lg-10">
            <select class="form-control" name="feed_type" id="feed-type">
                {foreach $feed_types as $row}
                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/contact"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}