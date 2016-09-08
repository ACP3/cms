{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    {redirect_message}
    <form action="{uri args="acp/feeds"}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.input_text.tpl" name="feed_image" value=$form.feed_image maxlength=120 label={lang t="feeds|feed_image"}}
        {include file="asset:System/Partials/form_group.select.tpl" options=$feed_types required=true label={lang t="feeds|feed_type"}}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/feeds"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
