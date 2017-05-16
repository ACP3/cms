{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="search_term" value=$form.search_term required=true label={lang t="search|search_term"}}
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="button" id="search-advanced-toggle" class="btn btn-default">
                {lang t="search|advanced_search"}
            </button>
        </div>
    </div>
    <div id="search-advanced-wrapper" class="hidden">
        <div class="form-group">
            <label class="col-sm-2 control-label required">{lang t="search|search_after_modules"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $search_mods as $row}
                        <label for="mods-{$row.dir}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="checkbox" name="mods[]" id="mods-{$row.dir}" value="{$row.dir}"{$row.checked}>
                            {$row.name}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$search_areas required=true label={lang t="search|search_after_areas"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$sort_hits required=true label={lang t="search|sort_hits"}}
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" submit_label={lang t="search|submit_search"}}
    {javascripts}
        {include_js module="search" file="frontend/index.index"}
    {/javascripts}
{/block}
