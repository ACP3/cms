{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|publication_period"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id_start}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="row mb-3" data-datepicker-range='{$datepicker.config|json_encode}'>
        <div class="col-sm-6">
            {if $datepicker.config.enableTime}
                <input class="form-control"
                       type="datetime-local"
                       id="{$datepicker.id_start}"
                       name="{$datepicker.name_start}"
                       value="{$datepicker.value_start}"
                       title="{lang t="system|start_date"}"
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}"
                       required>
            {else}
                <input class="form-control"
                       type="date"
                       id="{$datepicker.id_start}"
                       name="{$datepicker.name_start}"
                       value="{$datepicker.value_start}"
                       title="{lang t="system|start_date"}"
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
            {/if}
        </div>
        <div class="col-sm-6">
            {if $datepicker.config.enableTime}
                <input class="form-control"
                       type="datetime-local"
                       id="{$datepicker.id_end}"
                       name="{$datepicker.name_end}"
                       value="{$datepicker.value_end}"
                       title="{lang t="system|end_date"}"
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}"
                       required>
            {else}
                <input class="form-control"
                       type="date"
                       id="{$datepicker.id_end}"
                       name="{$datepicker.name_end}"
                       value="{$datepicker.value_end}"
                       title="{lang t="system|end_date"}"
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                       required>
            {/if}
        </div>
    </div>
    <span class="form-text">{lang t="system|date_description"}</span>
{/block}
