{if $access_check.mode == 'link'}
    <a href="{$access_check.uri}"
       title="{$access_check.lang}"
       class="btn btn-light {$access_check.selectors}"
       {if $access_check.blank}target="_blank"{/if}>
        {if $access_check.iconSet !== null && $access_check.icon !== null}
            {icon iconSet=$access_check.iconSet icon=$access_check.icon cssSelectors=$access_check.class title=$access_check.title}
        {elseif $access_check.class !== null}
            <i class="{$access_check.class}" aria-hidden="true"></i>
        {/if}
        {if $access_check.title !== null}
            {$access_check.title}
        {else}
            {$access_check.lang}
        {/if}
    </a>
{elseif $access_check.mode == 'button'}
    <button type="submit" class="btn btn-link {$access_check.selectors}" title="{$access_check.lang}">
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
