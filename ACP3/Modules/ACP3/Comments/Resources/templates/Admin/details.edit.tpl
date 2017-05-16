{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {if !isset($form.user_id)}
        {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=20 label={lang t="system|name"}}
    {elseif $form.user_id != '0'}
        <input type="hidden" name="user_id" value="{$form.user_id}">
    {/if}
    <div class="form-group">
        <label for="message" class="col-sm-2 control-label required">{lang t="system|message"}</label>

        <div class="col-sm-10">
            {if $can_use_emoticons}
                {event name="emoticons.render_emoticons_list"}
            {/if}
            <textarea class="form-control" name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/comments/details/index/id_`$module_id`"}}
{/block}
