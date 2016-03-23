{if is_array($js)}
    {if is_array($js.config)}
        {include file="asset:`$js.template`" config=$js.config}
    {else}
        {$js.config}
        {include file="asset:`$js.template`"}
    {/if}
{else}
    {$js}
{/if}
