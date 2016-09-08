{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="width" class="col-sm-2 control-label required">{lang t="categories|image_width"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="width" id="width" value="{$form.width}" required>

                <p class="help-block">{lang t="system|statements_in_pixel"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="height" class="col-sm-2 control-label required">{lang t="categories|image_height"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="height" id="height" value="{$form.height}" required>

                <p class="help-block">{lang t="system|statements_in_pixel"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="filesize" class="col-sm-2 control-label required">{lang t="categories|image_filesize"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="filesize" id="filesize" value="{$form.filesize}" required>

                <p class="help-block">{lang t="system|statements_in_byte"}</p>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/categories"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
