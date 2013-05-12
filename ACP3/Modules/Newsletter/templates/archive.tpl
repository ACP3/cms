{if isset($newsletters)}
<script type="text/javascript">
$(document).ready(function() {
	$('#newsletter-submit').hide();
	$('#newsletters').on('change', function() {
		$(this).closest('form').submit();
	});
});
</script>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<form action="{$REQUEST_URI}" method="post" class="navbar-form pull-right">
			<select id="newsletters" name="newsletter">
				<option value="">{lang t="system|pls_select"}</option>
{foreach $newsletters as $row}
				<option value="{$row.id}"{$row.selected}>{$row.title} - {$row.date_formatted}</option>
{/foreach}
			</select>
			<input type="submit" name="categories" value="{lang t="system|submit"}" id="newsletter-submit" class="btn">
		</form>
	</div>
</div>
{if isset($newsletter)}
<article class="dataset-box">
	<header class="header">
		<small class="pull-right"><time datetime="{$newsletter.date_iso}">{$newsletter.date_formatted}</time></small>
		<h1>{$newsletter.title}</h1>
	</header>
	<div class="content">
		{$newsletter.text}
	</div>
</article>
{else}
<div class="alert align-center">
	<strong>{lang t="newsletter|select_newsletter"}</strong>
</div>
{/if}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}