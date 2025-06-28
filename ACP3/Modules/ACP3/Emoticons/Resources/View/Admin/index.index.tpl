{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/emoticons/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/emoticons/index/create', 'button_icon' => 'plus', 'button_icon_selectors' => 'text-success', 'button_selectors' => ' ', 'edit_path' => "acp/emoticons/index/create/", 'title' => {lang t="emoticons|admin_index_create"}]}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/emoticons/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/emoticons/index/settings/", 'title' => {lang t="emoticons|admin_index_settings"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
