{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/captcha/index/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "acp/captcha/index/settings/", 'title' => {lang t="captcha|admin_index_settings"}]}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
