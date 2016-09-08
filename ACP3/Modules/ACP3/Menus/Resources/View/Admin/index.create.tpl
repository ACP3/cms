{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label required">{lang t="menus|menu_bar_title"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
            </div>
        </div>
        <div class="form-group">
            <label for="index-name" class="col-sm-2 control-label required">{lang t="menus|index_name"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20" required>

                <p class="help-block">{lang t="menus|index_name_description"}</p>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/menus"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
