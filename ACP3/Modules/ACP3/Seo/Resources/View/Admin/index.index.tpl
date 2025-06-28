{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/seo/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/seo/index/create', 'button_icon' => 'plus', 'button_icon_selectors' => 'text-success', 'button_selectors' => ' ', 'edit_path' => "acp/seo/index/create/", 'title' => {lang t="seo|admin_index_create"}]}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/seo/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/seo/index/settings/", 'title' => {lang t="seo|admin_index_settings"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
