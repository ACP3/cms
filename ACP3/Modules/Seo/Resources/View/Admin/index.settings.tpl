{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="seo-title" class="col-sm-2 control-label">{lang t="system|title"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="seo_title" id="seo-title" value="{$form.seo_title}" maxlength="120">
            </div>
        </div>
        <div class="form-group">
            <label for="seo-meta-description" class="col-sm-2 control-label">{lang t="seo|description"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="seo_meta_description" id="seo-meta-description" value="{$form.seo_meta_description}" maxlength="120">
            </div>
        </div>
        <div class="form-group">
            <label for="seo-meta-keywords" class="col-sm-2 control-label">{lang t="seo|keywords"}</label>

            <div class="col-sm-10">
                <textarea class="form-control" name="seo_meta_keywords" id="seo-meta-keywords" cols="50" rows="6">{$form.seo_meta_keywords}</textarea>

                <p class="help-block">{lang t="seo|keywords_separate_with_commas"}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="seo-robots" class="col-sm-2 control-label">{lang t="seo|robots"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="seo_robots" id="seo-robots">
                    {foreach $robots as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$mod_rewrite.0.id}" class="col-sm-2 control-label">{lang t="seo|mod_rewrite"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $mod_rewrite as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="seo_mod_rewrite" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
                <p class="help-block">{lang t="seo|mod_rewrite_description"}</p>
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
    {include_js module="system" file="forms"}
    {/javascripts}
{/block}