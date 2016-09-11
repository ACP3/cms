{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {block AJAX_FORM_CONTENT_BEFORE}{/block}
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
        {block AJAX_FORM_CONTENT}{/block}
    </form>
    {javascripts}
    {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
