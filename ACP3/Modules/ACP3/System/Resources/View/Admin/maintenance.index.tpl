{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/system/maintenance/update_check" class="glyphicon glyphicon-refresh text-success"}
    {check_access mode="link" path="acp/system/maintenance/cache" class="glyphicon glyphicon-asterisk text-warning"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|select_menu_item"}</strong>
    </div>
{/block}
