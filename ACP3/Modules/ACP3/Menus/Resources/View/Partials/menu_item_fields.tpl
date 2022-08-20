{$titleFormFieldName=(isset($titleFormFieldName)) ? $titleFormFieldName : 'title'}
{include file="asset:System/Partials/form_group.input_text.tpl" name=$titleFormFieldName value=$form[$titleFormFieldName] labelRequired=true maxlength=120 label={lang t="menus|title"}}
{include file="asset:System/Partials/form_group.select.tpl" options=$blocks labelRequired=true label={lang t="menus|menu_bar"}}
{include file="asset:Menus/Partials/form_group.parent_menu_item.tpl" name="parent_id" menuItems=$menuItems label={lang t="menus|superior_page"} emptyOptionLabel={lang t="menus|no_superior_page"}}
{include file="asset:System/Partials/form_group.button_group.tpl" options=$display required=true label={lang t="menus|display_item"}}
{javascripts}
    {include_js module="menus" file="partials/manage-menu-item"}
{/javascripts}
