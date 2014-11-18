{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="width" class="col-sm-2 control-label">{lang t="emoticons|image_width"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="width" id="width" value="{$form.width}">

                <p class="help-block">{lang t="system|statements_in_pixel"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="height" class="col-sm-2 control-label">{lang t="emoticons|image_height"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="height" id="height" value="{$form.height}">

                <p class="help-block">{lang t="system|statements_in_pixel"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="filesize" class="col-sm-2 control-label">{lang t="emoticons|image_filesize"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="filesize" id="filesize" value="{$form.filesize}">

                <p class="help-block">{lang t="system|statements_in_byte"}</p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/emoticons"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}