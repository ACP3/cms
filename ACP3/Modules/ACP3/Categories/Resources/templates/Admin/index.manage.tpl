{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength="120" label={lang t="categories|title"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description maxlength="120" label={lang t="system|description"}}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="categories|picture"}}
    {if !empty($form.module_id)}
        <input type="hidden" name="module_id" value="{$form.module_id}">
    {else}
        <div class="form-group">
            <label for="module-id" class="col-sm-2 control-label required">{lang t="categories|module"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="module_id" id="module-id" required>
                    {foreach $mod_list as $row}
                        <option value="{$row.id}"{$row.selected}>{$row.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {/if}
    {if !empty($category_tree)}
        <div class="form-group">
            <label for="parent-id" class="col-sm-2 control-label">{lang t="categories|superior_category"}</label>
            <div class="col-sm-10">
                <select class="form-control" name="parent_id" id="parent-id">
                    <option value="">{lang t="categories|no_superior_category"}</option>
                    {foreach $category_tree as $row}
                        <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {else}
        <input type="hidden" name="parent_id" value="{$form.parent_id}">
    {/if}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/categories"}}
{/block}
