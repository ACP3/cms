{extends file="asset:layout.tpl"}

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
        {include file="asset:seo/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/seo"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}