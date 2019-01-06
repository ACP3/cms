{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR}
    {check_access mode="link" path="acp/system/extensions/modules" class="fas fa-puzzle-piece" btn_class="btn btn-secondary"}
    {check_access mode="link" path="acp/system/extensions/designs" class="fas fa-palette" btn_class="btn btn-secondary"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
