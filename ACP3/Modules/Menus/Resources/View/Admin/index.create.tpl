{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label">{lang t="menus|menu_bar_title"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
            </div>
        </div>
        <div class="form-group">
            <label for="index-name" class="col-sm-2 control-label">{lang t="menus|index_name"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20" required>

                <p class="help-block">{lang t="menus|index_name_description"}</p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/menus"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
{/block}

{block JAVASCRIPTS append}
    {include_js module="system" file="forms"}
{/block}