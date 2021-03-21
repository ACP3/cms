{extends file="asset:System/Partials/form_group.base.tpl"}

{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|date"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id}-input"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="input-group date"
         id="{$datepicker.id}"
         data-datepicker='{$datepicker.config|json_encode}'>
        <input class="form-control"
               type="text"
               name="{$datepicker.name}"
               id="{$datepicker.id}-input"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}"
               data-input>
        <span class="input-group-addon" data-toggle>
            {icon iconSet="solid" icon="calendar"}
        </span>
    </div>
{/block}
