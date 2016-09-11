{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true label={lang t="newsletter|subject"}}
    {datepicker name="date" value=$form.date}
    <div class="form-group">
        <label for="text" class="col-sm-2 control-label required">{lang t="newsletter|text"}</label>

        <div class="col-sm-10">
            {if $settings.html == 1}
                {wysiwyg name="text" value="`$form.text`" height="250"}
            {else}
                <textarea class="form-control" name="text" id="text" cols="50" rows="5" required>{$form.text}</textarea>
            {/if}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$test required=true label={lang t="newsletter|test_newsletter"} help={lang t="newsletter|test_newsletter_description"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/newsletter"}}
{/block}
