{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="uri" class="col-sm-2 control-label required">{lang t="seo|uri"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="uri" id="uri" value="{$form.uri}" maxlength="120" required>
            </div>
        </div>
        {include file="asset:Seo/Partials/seo_fields.tpl" seo=$SEO_FORM_FIELDS disable_alias_suggest=true}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/seo"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
