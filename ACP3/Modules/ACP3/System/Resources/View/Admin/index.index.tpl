{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance" iconSet="solid" icon="wrench"}
    {check_access mode="link" path="acp/system/extensions" iconSet="solid" icon="circle-half-stroke"}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/system/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/system/index/settings/", 'title' => {lang t="system|admin_index_settings"}]}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
