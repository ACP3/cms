{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    {redirect_message}
    <form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="search-term" class="col-sm-2 control-label required">{lang t="search|search_term"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="text" name="search_term" id="search-term" value="{$form.search_term}" required>
            </div>
        </div>
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
        <div class="form-group">
            <label for="{$search_areas.0.id}" class="col-sm-2 control-label required">{lang t="search|search_after_areas"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $search_areas as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="area" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="{$sort_hits.0.id}" class="col-sm-2 control-label required">{lang t="search|sort_hits"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $sort_hits as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="sort" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="search|submit_search"}</button>
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}