{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance/update_check" iconSet="solid" icon="sync-alt" class="text-success"}
    {check_access mode="link" path="acp/system/maintenance/cache" iconSet="solid" icon="tachometer-alt" class="text-warning"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
