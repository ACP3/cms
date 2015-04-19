{javascripts}
    {include_js module="system" file="datepicker" depends="datetimepicker"}
{/javascripts}
{if $datepicker.range == 1}
    {if $datepicker.input_only}
        <div class="row" data-datepicker-range='{$datepicker.range_json}'>
            <div class="col-sm-6">
                <div class="input-group date" id="{$datepicker.id_start}">
                    <input class="form-control"
                           type="text"
                           id="{$datepicker.id_start}-input"
                           name="{$datepicker.name_start}"
                           value="{$datepicker.value_start}"
                           maxlength="{$datepicker.length}"
                           title="{lang t="system|start_date"}"
                           data-date-format="{$datepicker.params.format}"
                           data-date-picktime="{$datepicker.with_time}"
                           required>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
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
                           data-date-format="{$datepicker.params.format}"
                           data-date-picktime="{$datepicker.with_time}"
                           required>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <span class="help-block">{lang t="system|date_description"}</span>
    {else}
        <div class="form-group">
            <label for="{$datepicker.id_start}-input" class="col-sm-2 control-label">{lang t="system|publication_period"}</label>
            <div class="col-sm-10">
                <div class="row" data-datepicker-range='{$datepicker.range_json}'>
                    <div class="col-sm-6">
                        <div class="input-group date" id="{$datepicker.id_start}">
                            <input class="form-control"
                                   type="text"
                                   id="{$datepicker.id_start}-input"
                                   name="{$datepicker.name_start}"
                                   value="{$datepicker.value_start}"
                                   maxlength="{$datepicker.length}"
                                   title="{lang t="system|start_date"}"
                                   data-date-format="{$datepicker.params.format}"
                                   data-date-picktime="{$datepicker.with_time}"
                                   required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
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
                                   data-date-format="{$datepicker.params.format}"
                                   data-date-picktime="{$datepicker.with_time}"
                                   required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <span class="help-block">{lang t="system|date_description"}</span>
            </div>
        </div>
    {/if}
{else}
    {if $datepicker.input_only}
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
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    {else}
        <div class="form-group">
            <label for="{$datepicker.id}-input" class="col-sm-2 control-label">{lang t="system|date"}</label>
            <div class="col-sm-10">
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
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
    {/if}
{/if}