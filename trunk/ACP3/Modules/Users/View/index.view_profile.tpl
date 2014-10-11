<fieldset>
    <legend>{lang t="system|general"}</legend>
    <dl class="dl-horizontal">
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
            <dd>{$user.birthday}</dd>
        {/if}
    </dl>
</fieldset>
{if $user.mail_display == 1 || !empty($user.icq) || !empty($user.skype) || !empty($user.website)}
    <fieldset>
        <legend>{lang t="users|contact"}</legend>
        <dl class="dl-horizontal">
            {if $user.mail_display == 1}
                <dt>{lang t="system|email_address"}</dt>
                <dd>{mailto address=$user.mail encode="javascript"}</a></dd>
            {/if}
            {if !empty($user.icq)}
                <dt>{lang t="users|icq"}</dt>
                <dd>
                    <a href="http://www.icq.com/{$user.icq}" onclick="window.open(this.href); return false">
                        <img src="http://web.icq.com/whitepages/online?icq={$user.icq}&amp;img=27" alt="">
                        {$user.icq}
                    </a>
                </dd>
            {/if}
            {if !empty($user.skype)}
                <dt>{lang t="users|skype"}</dt>
                <dd>
                    <a href="skype:{$user.skype}?userinfo" onclick="window.open(this.href); return false">
                        <img src="http://mystatus.skype.com/smallicon/{$user.skype}" alt="">
                        {$user.skype}
                    </a>
                </dd>
            {/if}
            {if !empty($user.website)}
                <dt>{lang t="system|website"}</dt>
                <dd>
                    <a href="{$user.website}" rel="nofollow" onclick="window.open(this.href); return false" title="{lang t="users|visit_website"}">{$user.website}</a>
                </dd>
            {/if}
        </dl>
    </fieldset>
{/if}
{if $user.address_display == 1 || $user.country_display == 1}
    <fieldset>
        <legend>{lang t="users|address"}</legend>
        <dl class="dl-horizontal">
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