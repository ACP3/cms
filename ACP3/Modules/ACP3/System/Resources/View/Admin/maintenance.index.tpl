{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR}
    {check_access mode="link" path="acp/system/maintenance/update_check" class="fas fa-sync" btn_class="btn btn-success"}
    {check_access mode="link" path="acp/system/maintenance/cache" class="fas fa-asterisk" btn_class="btn btn-warning"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
