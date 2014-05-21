<p>
    {$LANG_users_found}
</p>{if isset($users)}
{$pagination}
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
                <td>{if !empty($row.website)}
                        <a href="{$row.website}" rel="nofollow" onclick="window.open(this.href); return false" title="{lang t="users|visit_website"}">{$row.website}</a>{else}-{/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>{/if}