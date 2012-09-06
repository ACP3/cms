{js_libraries enable="jquery-ui{if $datepicker.with_time == 1},timepicker{/if}"}
<script type="text/javascript">
$(document).ready(function() {
{if $datepicker.range == 1}
{if $datepicker.with_time == 1}
	$('#{$datepicker.name_start}, #{$datepicker.name_end}').datetimepicker({
{else}
	$('#{$datepicker.name_start}, #{$datepicker.name_end}').datepicker({
{/if}
{else}
{if $datepicker.with_time == 1}
	$('#{$datepicker.name}').datetimepicker({
{else}
	$('#{$datepicker.name}').datepicker({
{/if}
{/if}
{foreach $datepicker.params as $paramKey => $paramValue}
		{$paramKey}: {$paramValue},
{/foreach}
	});
});
</script>
{if $datepicker.range == 1}
{if $datepicker.input_only}
<input type="text" name="{$datepicker.name_start}" id="{$datepicker.name_start}" value="{$datepicker.value_start}" maxlength="{$datepicker.length}" title="{lang t="system|start_date"}" required style="margin-right:4px">
-
<input type="text" name="{$datepicker.name_end}" id="{$datepicker.name_end}" value="{$datepicker.value_end}" maxlength="{$datepicker.length}" title="{lang t="system|end_date"}" required style="margin-right:4px">
<p class="help-block">{lang t="system|date_description"}</p>
{else}
<div class="control-group">
	<label for="{$datepicker.name_start}" class="control-label">{lang t="system|publication_period"}</label>
	<div class="controls">
		<input type="text" name="{$datepicker.name_start}" id="{$datepicker.name_start}" value="{$datepicker.value_start}" maxlength="{$datepicker.length}" title="{lang t="system|start_date"}" required style="margin-right:4px">
		-
		<input type="text" name="{$datepicker.name_end}" id="{$datepicker.name_end}" value="{$datepicker.value_end}" maxlength="{$datepicker.length}" title="{lang t="system|end_date"}" required style="margin-right:4px">
		<p class="help-block">{lang t="system|date_description"}</p>
	</div>
</div>
{/if}
{else}
{if $datepicker.input_only}
<input type="text" name="{$datepicker.name}" id="{$datepicker.name}" value="{$datepicker.value}" maxlength="{$datepicker.length}" style="margin-right:4px">
{else}
<div class="control-group">
	<label for="{$datepicker.name}" class="control-label">{lang t="system|date"}</label>
	<div class="controls">
		<input type="text" name="{$datepicker.name}" id="{$datepicker.name}" value="{$datepicker.value}" maxlength="{$datepicker.length}" style="margin-right:4px">
	</div>
</div>
{/if}
{/if}