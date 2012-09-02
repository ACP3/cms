<div class="alert">
	<strong>{lang t="failure"}</strong>
	<ul>
{foreach $errors as $row}
		<li>{$row}</li>
{/foreach}
	</ul>
</div>