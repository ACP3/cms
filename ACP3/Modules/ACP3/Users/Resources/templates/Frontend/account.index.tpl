{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="users/account/edit" class="glyphicon glyphicon-pencil text-info"}
    {check_access mode="link" path="users/account/settings" class="glyphicon glyphicon-cog"}
    {event name="users.account.index.header_bar"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {$dashboard={event name="user.account.index.dashboard"}}
    {if !empty($dashboard)}
        {$dashboard}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
    {/if}
{/block}
