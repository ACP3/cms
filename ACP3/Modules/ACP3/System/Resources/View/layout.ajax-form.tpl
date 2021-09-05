{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {block CONTENT_BEFORE_AJAX_FORM}
        {redirect_message}
    {/block}
    <form action="{block CONTENT_AJAX_FORM_URI}{$REQUEST_URI}{/block}"
          method="post"
          accept-charset="UTF-8"
          novalidate
          {if isset($is_multipart) && $is_multipart === true}enctype="multipart/form-data"{/if}
          {if isset($scroll_offset_element)}data-scroll-offset-element="{$scroll_offset_element}"{/if}
          {if isset($form_id)}id="{$form_id}"{/if}
          data-ajax-form="true"
          data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {block CONTENT_AJAX_FORM}{/block}
    </form>
    {js_libraries enable="ajax-form"}
{/block}
