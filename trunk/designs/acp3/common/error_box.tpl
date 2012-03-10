{if $error_box.non_integer_keys === true}
<script type="text/javascript">
$(document).ready(function() {
	$('.error-box ul li').each(function() {
		var errorClass = $(this).attr('class');
		if (errorClass.length > 0) {
			$('#' + errorClass.substr(6)).addClass('error');
		}
	});
});
</script>
{/if}
<div class="error-box" style="margin:2px auto 10px">
	<h4>{lang t="common|failure"}</h4>
	<ul>
{foreach $error_box.errors as $key => $value}
		<li{if ACP3_validate::isNumber($key) === false} class="error-{$key}"{/if}>{$value}</li>
{/foreach}
	</ul>
</div>