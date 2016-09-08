{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.input_text.tpl" name="code" value=$form.code required=true maxlength=10 label={lang t="emoticons|code"}}
        {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description required=true maxlength=15 label={lang t="system|description"}}
        {block EMOTICONS_PICTURE_UPLOAD}
            <div class="form-group">
                <label for="picture" class="col-sm-2 control-label required">{lang t="emoticons|picture"}</label>

                <div class="col-sm-10"><input type="file" name="picture" id="picture" required></div>
            </div>
        {/block}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
