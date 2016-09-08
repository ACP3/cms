{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label required">{lang t="newsletter|subject"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="title" id="title" value="{$form.title}" required></div>
        </div>
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
        <div id="test-newsletter" class="form-group">
            <label for="test-1" class="col-sm-2 control-label required">{lang t="newsletter|test_newsletter"}</label>
            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $test as $row}
                        <label for="test-{$row.value}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="test" id="test-{$row.value}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
                <p class="help-block">{lang t="newsletter|test_newsletter_description"}</p>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/newsletter"}}
    </form>
    {javascripts}
        {include_js module="newsletter" file="admin/acp"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
