{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/newsletter/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/newsletter/index/create', 'button_icon' => 'plus', 'button_icon_selectors' => 'text-success', 'button_selectors' => ' ', 'edit_path' => "acp/newsletter/index/create/", 'title' => {lang t="newsletter|admin_index_create"}]}
    {check_access mode="link" path="acp/newsletter/accounts" iconSet="solid" icon="users" class="text-info"}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/newsletter/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/newsletter/index/settings/", 'title' => {lang t="newsletter|admin_index_settings"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
