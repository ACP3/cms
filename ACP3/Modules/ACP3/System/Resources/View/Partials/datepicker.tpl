{if $datepicker.range == 1}
    {include file="asset:System/Partials/datepicker.range.tpl" datepicker=$datepicker}
    {javascripts}
    {include_js module="system" file="partials/daterangepicker" depends="flatpickr"}
    {/javascripts}
{else}
    {include file="asset:System/Partials/datepicker.single.tpl" datepicker=$datepicker}
    {javascripts}
        {include_js module="system" file="partials/datepicker" depends="flatpickr"}
    {/javascripts}
{/if}
