{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance" class="fa fa-wrench"}
    {check_access mode="link" path="acp/system/extensions" class="fa fa-adjust"}
    {check_access mode="link" path="acp/system/index/settings" class="fa fa-cog"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}