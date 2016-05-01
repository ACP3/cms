{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label required">{lang t="emoticons|code"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="code" id="code" value="{$form.code}" maxlength="10"></div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label required">{lang t="system|description"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="description" id="description" value="{$form.description}" maxlength="15">
            </div>
        </div>
        {block EMOTICONS_PICTURE_UPLOAD}
            <div class="form-group">
                <label for="picture" class="col-sm-2 control-label required">{lang t="emoticons|picture"}</label>

                <div class="col-sm-10"><input type="file" name="picture" id="picture"></div>
            </div>
        {/block}
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
