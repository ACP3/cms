{extends file="asset:System/Partials/form_group.base.tpl"}

{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|date"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    {if $datepicker.config.enableTime}
        <input class="form-control"
               type="datetime-local"
               name="{$datepicker.name}"
               id="{$datepicker.id}"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}"
               pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}">
    {else}
        <input class="form-control"
               type="date"
               name="{$datepicker.name}"
               id="{$datepicker.id}"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}"
               pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
    {/if}
{/block}
