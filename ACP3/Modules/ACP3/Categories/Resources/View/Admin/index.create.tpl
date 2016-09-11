{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength="120" label={lang t="categories|title"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description required=true maxlength="120" label={lang t="system|description"}}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="categories|picture"}}
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
{/block}
