{if isset($newsletters)}
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<form action="{$REQUEST_URI}" method="post" class="navbar-form pull-right">
			<select name="newsletter">
				<option value="">{lang t="system|pls_select"}</option>
{foreach $newsletters as $row}
				<option value="{$row.id}"{$row.selected}>{$row.title} - {$row.date_formatted}</option>
{/foreach}
			</select>
			<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		</form>
	</div>
</div>
{if isset($newsletter)}
<div class="dataset-box">
	<div class="header">
		<div class="pull-right small">{$newsletter.date_formatted}</div>
		{$newsletter.title}
	</div>
	<div class="content">
		{$newsletter.text}
	</div>
</div>
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