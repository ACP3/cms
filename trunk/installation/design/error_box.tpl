<div class="alert alert-block">
	<h4 class="aler-heading">{lang t="common|failure"}</h4>
	<ul>
{foreach $errors as $row}
		<li>{$row}</li>
{/foreach}
	</ul>
</div>