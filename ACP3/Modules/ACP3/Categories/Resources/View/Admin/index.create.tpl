{extends file="asset:`$LAYOUT`"}

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
        {block CATEGORIES_MODULE}
            <div class="form-group">
                <label for="module" class="col-sm-2 control-label required">{lang t="categories|module"}</label>

                <div class="col-sm-10">
                    <select class="form-control" name="module" id="module" required>
                        {foreach $mod_list as $row}
                            <option value="{$row.id}"{$row.selected}>{$row.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/block}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/categories"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
