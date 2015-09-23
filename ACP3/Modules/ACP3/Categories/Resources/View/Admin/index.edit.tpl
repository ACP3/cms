{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label required">{lang t="categories|title"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label required">{lang t="system|description"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="description" id="description" value="{$form.description}" maxlength="120" required>
            </div>
        </div>
        <div class="form-group">
            <label for="picture" class="col-sm-2 control-label">{lang t="categories|picture"}</label>

            <div class="col-sm-10"><input type="file" id="picture" name="picture"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/categories"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}