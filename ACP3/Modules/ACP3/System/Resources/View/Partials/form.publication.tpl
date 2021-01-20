{include file="asset:System/Partials/form_group.button_group.tpl" options=$options name="active" required=true label={lang t="system|active"}}
<div id="publication-period-wrapper">
    {datepicker name=['start', 'end'] value=$publication_period}
</div>
{javascripts}
    {include_js module="system" file="partials/form.publication"}
{/javascripts}
