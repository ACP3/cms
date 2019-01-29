{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR}
    {check_access mode="link" path="users/account/edit" class="fas fa-user-edit" btn_class="btn btn-primary" title={lang t="users|frontend_account_edit"}}
    {check_access mode="link" path="users/account/settings" class="fas fa-cog" btn_class="btn btn-secondary"}
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
