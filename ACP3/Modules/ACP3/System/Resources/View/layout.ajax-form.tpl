{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {block CONTENT_BEFORE_AJAX_FORM}{/block}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}"
          method="post"
          accept-charset="UTF-8"
          {if isset($is_multipart) && $is_multipart === true}enctype="multipart/form-data"{/if}
          class="form-horizontal"
          data-ajax-form="true"
          data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {block CONTENT_AJAX_FORM}{/block}
    </form>
    {javascripts}
    {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
