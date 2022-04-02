{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance" iconSet="solid" icon="wrench"}
    {check_access mode="link" path="acp/system/extensions" iconSet="solid" icon="circle-half-stroke"}
    {check_access mode="link" path="acp/system/index/settings" iconSet="solid" icon="gear"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
