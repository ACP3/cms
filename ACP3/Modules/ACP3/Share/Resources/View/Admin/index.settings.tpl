{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.select.tpl" options=$services multiple=true required=true label={lang t="share|active_services"}}
    <div id="fb-credentials-wrapper">
        {include file="asset:System/Partials/form_group.input_text.tpl" name="fb_app_id" value=$form.fb_app_id maxlength=255 label={lang t="share|fb_app_id"}}
        {include file="asset:System/Partials/form_group.input_password.tpl" name="fb_secret" value=$form.fb_secret maxlength=255 label={lang t="share|fb_secret"}}
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/share"}}
    {javascripts}
    {include_js module="share" file="admin/index.settings"}
    {/javascripts}
{/block}
