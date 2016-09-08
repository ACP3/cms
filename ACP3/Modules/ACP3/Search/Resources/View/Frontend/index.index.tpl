{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    {redirect_message}
    <form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.input_text.tpl" name="search_term" value=$form.search_term required=true label={lang t="search|search_term"}}
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
        {include file="asset:System/Partials/form_group.submit.tpl" submitLabel={lang t="search|submit_search"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
