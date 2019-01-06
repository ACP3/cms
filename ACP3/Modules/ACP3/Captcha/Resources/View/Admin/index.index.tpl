{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR}
    {check_access mode="link" path="acp/captcha/index/settings" class="fas fa-cog" btn_class="btn btn-secondary" title={lang t="captcha|admin_index_settings"}}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
