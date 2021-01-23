{tab title={lang t="menus|menus"}}
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
    <div id="manage-menu-item-container">
        {include file="asset:Menus/Partials/menu_item_fields.tpl"}
    </div>
    <input type="hidden" name="menu_item_uri_pattern" value="{$uri_pattern}">
{/tab}
