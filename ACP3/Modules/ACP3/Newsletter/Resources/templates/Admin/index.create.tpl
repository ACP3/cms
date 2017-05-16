{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true label={lang t="newsletter|subject"}}
    {datepicker name="date" value=$form.date}
    {if $settings.html == 1}
        {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true label={lang t="newsletter|text"}}
    {else}
        {include file="asset:System/Partials/form_group.textarea.tpl" name="text" value=$form.text required=true label={lang t="newsletter|text"}}
    {/if}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$test required=true label={lang t="newsletter|test_newsletter"} help={lang t="newsletter|test_newsletter_description"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/newsletter"}}
{/block}
