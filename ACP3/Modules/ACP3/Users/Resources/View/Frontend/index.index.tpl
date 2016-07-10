{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="users|users_found" args=['%count%' => $all_users]}
    </p>
    {if !empty($users)}
        {include file="asset:System/pagination.tpl" pagination=$pagination}
        <table class="table table-striped">
            <thead>
            <tr>
                <th>{lang t="users|nickname"}</th>
                <th>{lang t="users|realname"}</th>
                <th>{lang t="system|email_address"}</th>
                <th>{lang t="system|website"}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $users as $row}
                <tr>
                    <td>
                        <a href="{uri args="users/index/view_profile/id_`$row.id`"}" title="{lang t="users|view_profile"}">{$row.nickname}</a>
                    </td>
                    <td>{if !empty($row.realname)}{$row.realname}{else}-{/if}</td>
                    <td>{if $row.mail_display == 1}{mailto address=$row.mail encode="javascript"}{else}-{/if}</td>
                    <td>
                        {if !empty($row.website)}
                            <a href="{$row.website|prefix_uri}" rel="nofollow" target="_blank" title="{lang t="users|visit_website"}">{$row.website}</a>
                        {else}
                            -
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
