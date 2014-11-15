{if $datepicker.range == 1}
    {if $datepicker.input_only}
        <div class="row" data-datepicker-range='{$datepicker.range_json}'>
            <div class="col-sm-6">
                <input class="form-control"
                       type="text"
                       name="{$datepicker.name_start}"
                       id="{$datepicker.name_start}"
                       value="{$datepicker.value_start}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|start_date"}"
                       required
                       data-date-format="{$datepicker.params.format}"
                       data-date-picktime="{$datepicker.with_time}">
            </div>
            <div class="col-sm-6">
                <input class="form-control"
                       type="text"
                       name="{$datepicker.name_end}"
                       id="{$datepicker.name_end}"
                       value="{$datepicker.value_end}"
                       maxlength="{$datepicker.length}"
                       title="{lang t="system|end_date"}"
                       required data-date-format="{$datepicker.params.format}"
                       data-date-picktime="{$datepicker.with_time}">
            </div>
        </div>
        <span class="help-block">{lang t="system|date_description"}</span>
    {else}
        <div class="form-group">
            <label for="{$datepicker.name_start}" class="col-sm-2 control-label">{lang t="system|publication_period"}</label>
            <div class="col-sm-10">
                <div class="row" data-datepicker-range='{$datepicker.range_json}'>
                    <div class="col-sm-6">
                        <input class="form-control"
                               type="text"
                               name="{$datepicker.name_start}"
                               id="{$datepicker.name_start}"
                               value="{$datepicker.value_start}"
                               maxlength="{$datepicker.length}"
                               title="{lang t="system|start_date"}"
                               required data-date-format="{$datepicker.params.format}"
                               data-date-picktime="{$datepicker.with_time}">
                    </div>
                    <div class="col-sm-6">
                        <input class="form-control"
                               type="text"
                               name="{$datepicker.name_end}"
                               id="{$datepicker.name_end}"
                               value="{$datepicker.value_end}"
                               maxlength="{$datepicker.length}"
                               title="{lang t="system|end_date"}"
                               required data-date-format="{$datepicker.params.format}"
                               data-date-picktime="{$datepicker.with_time}">
                    </div>
                </div>
                <span class="help-block">{lang t="system|date_description"}</span>
            </div>
        </div>
    {/if}
{else}
    {if $datepicker.input_only}
        <input class="form-control"
               type="text"
               name="{$datepicker.name}"
               id="{$datepicker.name}"
               value="{$datepicker.value}"
               maxlength="{$datepicker.length}"
               data-datepicker="#{$datepicker.name}"
               data-date-format="{$datepicker.params.format}"
               data-date-picktime="{$datepicker.with_time}">
    {else}
        <div class="form-group" data-datepicker="#{$datepicker.name}">
            <label for="{$datepicker.name}" class="col-sm-2 control-label">{lang t="system|date"}</label>
            <div class="col-sm-10">
                <input class="form-control"
                       type="text"
                       name="{$datepicker.name}"
                       id="{$datepicker.name}"
                       value="{$datepicker.value}"
                       maxlength="{$datepicker.length}"
                       data-date-format="{$datepicker.params.format}"
                       data-date-picktime="{$datepicker.with_time}">
            </div>
        </div>
    {/if}
{/if}