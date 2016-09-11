{javascripts}
    {include_js module="system" file="datepicker" depends="datetimepicker"}
{/javascripts}
{if $datepicker.range == 1}
    {include file="asset:System/Partials/datepicker.range.tpl" datepicker=$datepicker}
{else}
    {include file="asset:System/Partials/datepicker.single.tpl" datepicker=$datepicker}
{/if}
