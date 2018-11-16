{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$datepicker.input_only}
    {$label=(!empty($label)) ? $label : {lang t="system|publication_period"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$datepicker.id_start}-input"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="row" data-datepicker-range='{$datepicker.range_json}'>
        <div class="col-sm-6 mb-2 mb-sm-0">
            <div class="input-group date" id="{$datepicker.id_start}" data-target-input="nearest">
                <input class="form-control datetimepicker-input"
                       type="text"
                       id="{$datepicker.id_start}-input"
                       name="{$datepicker.name_start}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|start_date"}"
                       required>
                <div class="input-group-append" data-target="#{$datepicker.id_start}" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-calendar" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group date" id="{$datepicker.id_end}" data-target-input="nearest">
                <input class="form-control datetimepicker-input"
                       type="text"
                       id="{$datepicker.id_end}-input"
                       name="{$datepicker.name_end}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|end_date"}"
                       required>
                <div class="input-group-append" data-target="#{$datepicker.id_end}" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-calendar" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
    </div>
    <small class="form-text text-muted">{lang t="system|date_description"}</small>
{/block}
