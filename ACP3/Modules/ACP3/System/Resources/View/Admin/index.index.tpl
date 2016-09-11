{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance" class="glyphicon glyphicon-wrench"}
    {check_access mode="link" path="acp/system/extensions" class="glyphicon glyphicon-adjust"}
    {check_access mode="link" path="acp/system/index/configuration" class="glyphicon glyphicon-cog"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|select_menu_item"}</strong>
    </div>
{/block}
