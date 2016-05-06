{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label required">{lang t="system|title"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120">
            </div>
        </div>
        <div class="form-group">
            <label for="meta-description" class="col-sm-2 control-label">{lang t="seo|description"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="meta_description" id="meta-description" value="{$form.meta_description}" maxlength="120">
            </div>
        </div>
        <div class="form-group">
            <label for="meta-keywords" class="col-sm-2 control-label">{lang t="seo|keywords"}</label>

            <div class="col-sm-10">
                <textarea class="form-control" name="meta_keywords" id="meta-keywords" cols="50" rows="6">{$form.meta_keywords}</textarea>

                <p class="help-block">{lang t="seo|keywords_separate_with_commas"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="robots" class="col-sm-2 control-label required">{lang t="seo|robots"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="robots" id="robots">
                    {foreach $robots as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/seo"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
    {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
