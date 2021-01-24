{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="menu-item-admin-edit-form"}
        {tab title={lang t="system|general_statements"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$mode required=true label={lang t="menus|page_type"}}
            {include file="asset:Menus/Partials/menu_item_fields.tpl"}
        {/tab}
        {tab title={lang t="menus|page_type"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$modules formGroupId="module-container" labelRequired=true label={lang t="menus|module"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="uri" formGroupId="link-container" value=$form.uri labelRequired=true maxlength=120 label={lang t="menus|uri"} help={lang t="menus|dynamic_page_hints"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$target formGroupId="target-container" labelRequired=true label={lang t="menus|target_page"}}
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/menus"}}
    {javascripts}
        {include_js module="menus" file="admin/items.create"}
    {/javascripts}
{/block}
