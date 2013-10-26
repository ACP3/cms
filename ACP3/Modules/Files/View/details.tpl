<article>
	<section class="dataset-box">
		<header class="navbar navbar-default">
			<div class="navbar-header">
				<h2 class="navbar-brand">{$file.title}</h2>
			</div>
			<small class="navbar-text pull-right">
				<time datetime="{$file.date_iso}">{$file.date_formatted}</time>
			</small>
		</header>
		<div class="content">
			{$file.text}
		</div>
		<footer class="hyperlink">
			<a href="{uri args="files/details/id_`$file.id`/action_download"}" class="download-file">
				<i class="glyphicon glyphicon-download-alt"></i>
				{lang t="files|download_file"} ({$file.size})
			</a>
		</footer>
	</section>
	{if isset($comments)}
		{$comments}
	{/if}
</article>