{if $access_check.mode == 'link'}
    <a href="{$access_check.uri}" title="{$access_check.lang}">
        {if $access_check.iconSet !== null && $access_check.icon !== null}
            {icon iconSet=$access_check.iconSet icon=$access_check.icon cssSelectors=$access_check.class title=$access_check.title}
        {elseif $access_check.class !== null}
            <i class="{$access_check.class}" aria-hidden="true"></i>
        {/if}
        {if $access_check.title !== null}
            {$access_check.title}
        {/if}
    </a>
{elseif $access_check.mode == 'button'}
    <button type="submit" class="btn btn-link" title="{$access_check.lang}">
        {if $access_check.iconSet !== null && $access_check.icon !== null}
            {icon iconSet=$access_check.iconSet icon=$access_check.icon cssSelectors=$access_check.class title=$access_check.title}
        {elseif $access_check.class !== null}
            <i class="{$access_check.class}" aria-hidden="true"></i>
        {/if}
        {if $access_check.title !== null}
            {$access_check.title}
        {/if}
    </button>
{/if}
