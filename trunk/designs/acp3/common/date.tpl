<dl>
{if $datepicker.range == 1}
	<dt>
		<label for="{$datepicker.name_start}">{lang t="common|publication_period"}</label>
		<span>({lang t="common|date_description"})</span>
	</dt>
	<dd>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#{$datepicker.name_start}, #{$datepicker.name_end}').datepicker({
{foreach $datepicker.params as $paramKey => $paramValue}
				{$paramKey}: {$paramValue},
{/foreach}
			});
		});
		</script>
		<span style="white-space:nowrap">
			<input type="text" name="{$datepicker.name_start}" id="{$datepicker.name_start}" value="{$datepicker.value_start}" maxlength="16" title="{lang t="common|start_date"}" required style="width:45%;margin-right:4px;display:inline">
			-
			<input type="text" name="{$datepicker.name_end}" id="{$datepicker.name_end}" value="{$datepicker.value_end}" maxlength="16" title="{lang t="common|end_date"}" required style="width:45%;margin-right:4px;display:inline">
		</span>
	</dd>
{else}
	<dt><label for="{$datepicker.name}">{lang t="common|date"}</label></dt>
	<dd>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#{$datepicker.name}').datepicker({
{foreach $datepicker.params as $paramKey => $paramValue}
				{$paramKey}: {$paramValue},
{/foreach}
			});
		});
		</script>
		<span style="white-space:nowrap">
			<input type="text" name="{$datepicker.name}" id="{$datepicker.name}" value="{$datepicker.value}" maxlength="16" style="width:96%;margin-right:4px;display:inline">
		</span>
	</dd>
{/if}
</dl>
