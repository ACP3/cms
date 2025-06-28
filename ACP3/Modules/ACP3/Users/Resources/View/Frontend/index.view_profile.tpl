{extends file="asset:`$LAYOUT`"}

{block EDIT_CONTENT}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/users/index/edit', 'edit_path' => "acp/users/index/edit/id_`$user.id`/", 'title' => {lang t="users|admin_index_edit"}]}
{/block}

{block CONTENT}
    <fieldset>
        <legend>{lang t="system|general"}</legend>
        <dl>
            <dt>{lang t="users|nickname"}</dt>
            <dd>{$user.nickname}</dd>
            {if !empty($user.realname)}
                <dt>{lang t="users|realname"}</dt>
                <dd>{$user.realname}</dd>
            {/if}
            {if $user.gender != 0}
                <dt>{lang t="users|gender"}</dt>
                <dd>{$user.gender}</dd>
            {/if}
            {if !empty($user.birthday) && $user.birthday_display != 0}
                <dt>{lang t="users|birthday"}</dt>
                <dd>
                    {if $user.birthday_display == 1}
                        {date_format date=$user.birthday format="d.m.Y"}
                    {else}
                        {date_format date=$user.birthday format="d.m"}
                    {/if}
                </dd>
            {/if}
        </dl>
    </fieldset>
    {if $user.mail_display == 1 || !empty($user.website)}
        <fieldset>
            <legend>{lang t="users|contact"}</legend>
            <dl>
                {if $user.mail_display == 1}
                    <dt>{lang t="system|email_address"}</dt>
                    <dd>{mailto address=$user.mail encode="javascript"}</a></dd>
                {/if}
                {if !empty($user.website)}
                    <dt>{lang t="system|website"}</dt>
                    <dd>
                        <a href="{$user.website|prefix_uri}"
                           rel="noopener nofollow"
                           target="_blank"
                           title="{lang t="users|visit_website"}">{$user.website}</a>
                    </dd>
                {/if}
            </dl>
        </fieldset>
    {/if}
    {if $user.address_display == 1 || $user.country_display == 1}
        <fieldset>
            <legend>{lang t="users|address"}</legend>
            <dl>
                {if $user.address_display == 1}
                    {if !empty($user.street)}
                        <dt>{lang t="users|address_street"}</dt>
                        <dd>{$user.street}</dd>
                    {/if}
                    {if !empty($user.house_number)}
                        <dt>{lang t="users|address_house_number"}</dt>
                        <dd>{$user.house_number}</dd>
                    {/if}
                    {if !empty($user.zip)}
                        <dt>{lang t="users|address_zip"}</dt>
                        <dd>{$user.zip}</dd>
                    {/if}
                    {if !empty($user.city)}
                        <dt>{lang t="users|address_city"}</dt>
                        <dd>{$user.city}</dd>
                    {/if}
                {/if}
                {if $user.country_display == 1 && !empty($user.country)}
                    <dt>{lang t="users|country"}</dt>
                    <dd>{$user.country_formatted}</dd>
                {/if}
            </dl>
        </fieldset>
    {/if}
{/block}
