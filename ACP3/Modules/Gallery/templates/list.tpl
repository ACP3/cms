{if isset($galleries)}
{$pagination}
{foreach $galleries as $row}
<div class="dataset-box">
	<div class="navbar">
		<div class="navbar-inner navbar-text">
			<small class="pull-right">
				<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
			</small>
			<h2><a href="{uri args="gallery/pics/id_`$row.id`"}">{$row.title} ({$row.pics_lang})</a></h2>
		</div>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}