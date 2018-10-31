{extends file="asset:System/Partials/form_group.base.tpl"}

{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|date"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id}-input"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="input-group date"
         id="{$datepicker.id}"
         data-target-input="nearest"
         data-datepicker='{$datepicker.params}'>
        <input class="form-control datetimepicker-input"
               type="text"
               name="{$datepicker.name}"
               id="{$datepicker.id}-input"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}">
        <div class="input-group-append" data-target="#{$datepicker.id}" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fas fa-calendar" aria-hidden="true"></i></div>
        </div>
    </div>
{/block}
