{extends file="asset:System/Partials/form_group.base.tpl"}

{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|date"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id}-input"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="input-group date"
         id="{$datepicker.id}"
         data-datepicker="#{$datepicker.id}">
        <input class="form-control"
               type="text"
               name="{$datepicker.name}"
               id="{$datepicker.id}-input"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}"
               data-date-format="{$datepicker.params.format}"
               data-date-picktime="{$datepicker.with_time}">
        <span class="input-group-addon">
            <i class="fas fa-calendar" aria-hidden="true"></i>
        </span>
    </div>
{/block}
