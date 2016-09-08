{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="emoticons|image_width"} help={lang t="system|statements_in_pixel"}}
        {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="emoticons|image_height"} help={lang t="system|statements_in_pixel"}}
        {include file="asset:System/Partials/form_group.input_number.tpl" name="filesize" value=$form.filesize required=true label={lang t="emoticons|image_filesize"} help={lang t="system|statements_in_byte"}}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
