{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/contact/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/contact/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/contact/index/settings/", 'id' => 'js-modal-contact-settings', 'title' => {lang t="contact|admin_index_settings"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
