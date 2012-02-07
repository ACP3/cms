<div class="error" style="margin:2px auto 10px">
	<h4>{lang t="common|failure"}</h4>
	<ul>
{foreach $errors as $row}
		<li>{$row}</li>
{/foreach}
	</ul>
</div>