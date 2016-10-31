{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="users/account/edit" class="glyphicon glyphicon-pencil text-info"}
    {check_access mode="link" path="users/account/settings" class="glyphicon glyphicon-cog"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
{/block}
