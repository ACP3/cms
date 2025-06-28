{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/newsletter/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/newsletter/index/create" iconSet="solid" icon="plus" class="text-success"}
    {check_access mode="link" path="acp/newsletter/accounts" iconSet="solid" icon="users" class="text-info"}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/newsletter/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/newsletter/index/settings/", 'title' => {lang t="newsletter|admin_index_settings"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
