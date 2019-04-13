{include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
<div id="manage-menu-item-container">
    {include file="asset:System/Partials/form_group.input_text.tpl" name="menu_item_title" value=$form.menu_item_title labelRequired=true maxlength=120 label={lang t="menus|title"}}
    <div class="form-group row">
        <label for="block-id" class="col-sm-2 col-form-label required">{lang t="menus|menu_bar"}</label>

        <div class="col-sm-10">
            <select class="form-control" name="block_id" id="block-id" required>
                {foreach $blocks as $row}
                    <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="parent-id" class="col-sm-2 col-form-label required">{lang t="menus|superior_page"}</label>

        <div class="col-sm-10">
            <select class="form-control" name="parent_id" id="parent-id">
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
    <input type="hidden" name="menu_item_uri_pattern" value="{$uri_pattern}">
</div>
{javascripts}
    {include_js module="menus" file="partials/manage-menu-item"}
{/javascripts}
