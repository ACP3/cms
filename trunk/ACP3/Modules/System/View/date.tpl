{js_libraries enable="jquery-ui{if $datepicker.with_time == 1},timepicker{/if}"}
<script type="text/javascript">
    {if $datepicker.with_time == 1}
        {assign var="picker" value="datetimepicker"}
    {else}
        {assign var="picker" value="datepicker"}
    {/if}
    $(document).ready(function () {
    {if $datepicker.range == 1}
        var startDateTextBox = $('#{$datepicker.name_start}');
        var endDateTextBox = $('#{$datepicker.name_end}');

        startDateTextBox.{$picker}({
            {foreach $datepicker.params as $paramKey => $paramValue}
            {$paramKey}: {$paramValue},
            {/foreach}
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.{$picker}('getDate');
                    var testEndDate = endDateTextBox.{$picker}('getDate');
                    if (testStartDate > testEndDate) {
                        endDateTextBox.{$picker}('setDate', testStartDate);
                    }
                } else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.{$picker}('option', 'minDate', selectedDateTime);
            }
        });
        endDateTextBox.{$picker}({
            {foreach $datepicker.params as $paramKey => $paramValue}
            {$paramKey}: {$paramValue},
            {/foreach}
            minDate: new Date('{$datepicker.value_start_r}'),
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.{$picker}('getDate');
                    var testEndDate = endDateTextBox.{$picker}('getDate');
                    if (testStartDate > testEndDate) {
                        startDateTextBox.{$picker}('setDate', testEndDate);
                    }
                } else {
                    startDateTextBox.val(dateText);
                }
            }
        });

        startDateTextBox.{$picker}('setDate', new Date('{$datepicker.value_start_r}'));
        endDateTextBox.{$picker}('setDate', new Date('{$datepicker.value_end_r}'));
    {else}
        $('#{$datepicker.name}').{$picker}({
        {foreach $datepicker.params as $paramKey => $paramValue}
        {$paramKey}: {$paramValue},
        {/foreach}
        defaultValue: '{$datepicker.value}'
        });
    {/if}
    });
</script>{if $datepicker.range == 1}
    {if $datepicker.input_only}
        <div class="row">
            <div class="col-lg-6">
                <input class="form-control" type="text" name="{$datepicker.name_start}" id="{$datepicker.name_start}" value="{$datepicker.value_start}" maxlength="{$datepicker.length}" title="{lang t="system|start_date"}" required>
            </div>
            <div class="col-lg-6">
                <input class="form-control" type="text" name="{$datepicker.name_end}" id="{$datepicker.name_end}" value="{$datepicker.value_end}" maxlength="{$datepicker.length}" title="{lang t="system|end_date"}" required>
            </div>
        </div>
        <span class="help-block">{lang t="system|date_description"}</span>
    {else}
        <div class="form-group">
            <label for="{$datepicker.name_start}" class="col-lg-2 control-label">{lang t="system|publication_period"}</label>

            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-6">
                        <input class="form-control" type="text" name="{$datepicker.name_start}" id="{$datepicker.name_start}" value="{$datepicker.value_start}" maxlength="{$datepicker.length}" title="{lang t="system|start_date"}" required>
                    </div>
                    <div class="col-lg-6">
                        <input class="form-control" type="text" name="{$datepicker.name_end}" id="{$datepicker.name_end}" value="{$datepicker.value_end}" maxlength="{$datepicker.length}" title="{lang t="system|end_date"}" required>
                    </div>
                </div>
                <span class="help-block">{lang t="system|date_description"}</span>
            </div>
        </div>
    {/if}
{else}
    {if $datepicker.input_only}
        <input class="form-control" type="text" name="{$datepicker.name}" id="{$datepicker.name}" value="{$datepicker.value}" maxlength="{$datepicker.length}">
    {else}
        <div class="form-group">
            <label for="{$datepicker.name}" class="col-lg-2 control-label">{lang t="system|date"}</label>

            <div class="col-lg-10">
                <input class="form-control" type="text" name="{$datepicker.name}" id="{$datepicker.name}" value="{$datepicker.value}" maxlength="{$datepicker.length}">
            </div>
        </div>
    {/if}
{/if}