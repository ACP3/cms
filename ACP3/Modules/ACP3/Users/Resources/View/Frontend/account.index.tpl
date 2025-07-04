{extends file="asset:System/layout.header-bar.tpl"}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'frontend/users/account/edit', 'button_icon' => 'pen', 'button_icon_selectors' => 'text-info', 'button_selectors' => ' ', 'edit_path' => "users/account/edit/", 'title' => {lang t="users|frontend_account_edit"}]}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'frontend/users/account/settings', 'button_icon' => 'gear', 'button_selectors' => ' ', 'edit_path' => "users/account/settings/", 'title' => {lang t="users|frontend_account_settings"}]}
    {event name="users.account.index.header_bar"}
{/block}
{block CONTENT_AFTER_HEADER_BAR}
    {$dashboard={event name="user.account.index.dashboard"}}
    {if !empty($dashboard)}
        {$dashboard}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="system|select_menu_item"}}
    {/if}
{/block}
