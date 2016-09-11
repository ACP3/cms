{$is_multipart=true}

{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="code" value=$form.code required=true maxlength=10 label={lang t="emoticons|code"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description required=true maxlength=15 label={lang t="system|description"}}
    {block EMOTICONS_PICTURE_UPLOAD}
        <div class="form-group">
            <label for="picture" class="col-sm-2 control-label required">{lang t="emoticons|picture"}</label>

            <div class="col-sm-10"><input type="file" name="picture" id="picture" required></div>
        </div>
    {/block}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/emoticons"}}
{/block}
