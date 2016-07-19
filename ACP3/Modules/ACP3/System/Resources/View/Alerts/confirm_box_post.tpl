{extends file="asset:System/Alerts/confirm_box.tpl"}

{block CONFIRM_BOX_MODAL_FOOTER}
    <form action="{$confirm.forward}" method="post">
        {foreach $confirm.data as $key => $value}
            {if is_array($value)}
                {foreach $value as $key2 => $value2}
                    <input type="hidden" name="{$key}[{$key2}]" value="{$value2}">
                {/foreach}
            {else}
                <input type="hidden" name="{$key}" value="{$value}">
            {/if}
        {/foreach}
        {if isset($confirm.backward)}
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|yes"}</button>
            <a href="{$confirm.backward}" class="btn btn-default">{lang t="system|no"}</a>
        {else}
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|forward"}</button>
        {/if}
    </form>
{/block}
