{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|publication_period"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id_start}-input"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="row mb-3" data-datepicker-range='{$datepicker.config|json_encode}'>
        <div class="col-sm-6">
            <div class="input-group date" id="{$datepicker.id_start}">
                <input class="form-control"
                       type="text"
                       id="{$datepicker.id_start}-input"
                       name="{$datepicker.name_start}"
                       value="{$datepicker.value_start}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|start_date"}"
                       data-input
                       required>
                <span class="input-group-text" data-toggle>
                    {icon iconSet="solid" icon="calendar"}
                </span>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group date" id="{$datepicker.id_end}">
                <input class="form-control"
                       type="text"
                       id="{$datepicker.id_end}-input"
                       name="{$datepicker.name_end}"
                       value="{$datepicker.value_end}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|end_date"}"
                       data-input
                       required>
                <span class="input-group-text" data-toggle>
                    {icon iconSet="solid" icon="calendar"}
                </span>
            </div>
        </div>
    </div>
    <span class="form-text">{lang t="system|date_description"}</span>
{/block}
