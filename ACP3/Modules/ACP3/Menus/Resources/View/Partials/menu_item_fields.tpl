{$titleFormFieldName=(isset($titleFormFieldName)) ? $titleFormFieldName : 'title'}
{include file="asset:System/Partials/form_group.input_text.tpl" name=$titleFormFieldName value=$form[$titleFormFieldName] labelRequired=true maxlength=120 label={lang t="menus|title"}}
{include file="asset:System/Partials/form_group.select.tpl" options=$blocks labelRequired=true label={lang t="menus|menu_bar"}}
<div class="row mb-3">
    <label for="parent-id" class="col-md-2 col-form-label required">{lang t="menus|superior_page"}</label>

    <div class="col-md-10">
        <select class="form-select" name="parent_id" id="parent-id">
            <option value="">{lang t="menus|no_superior_page"}</option>
            {foreach $menuItems as $blocks}
                <optgroup label="{$blocks.title}">
                    {foreach $blocks.items as $row}
                        <option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
                    {/foreach}
                </optgroup>
            {/foreach}
        </select>
    </div>
</div>
{include file="asset:System/Partials/form_group.button_group.tpl" options=$display required=true label={lang t="menus|display_item"}}
{javascripts}
    {include_js module="menus" file="partials/manage-menu-item"}
{/javascripts}
