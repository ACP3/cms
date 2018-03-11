{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {block CONTENT_BEFORE_AJAX_FORM}
        {redirect_message}
    {/block}
    {if !empty($error_msg)}
        {$error_msg}
    {else}
        <form action="{$REQUEST_URI}"
              method="post"
              accept-charset="UTF-8"
              {if isset($is_multipart) && $is_multipart === true}enctype="multipart/form-data"{/if}
              {if !isset($form_horizontal) || $form_horizontal === true}class="form-horizontal"{/if}
              data-ajax-form="true"
              data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
            {block CONTENT_AJAX_FORM}{/block}
        </form>
    {/if}
{/block}
