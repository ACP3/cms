<article>
	<section class="dataset-box">
		<header class="header">
			<small class="pull-right">
				<time datetime="{$file.date_iso}">{$file.date_formatted}</time>
			</small>
			<h1>{$file.title}</h1>
		</header>
		<div class="content">
			{$file.text}
		</div>
		<footer class="hyperlink">
			<a href="{uri args="files/details/id_`$file.id`/action_download"}" class="download-file">
				<i class="icon-download-alt"></i>
				{lang t="files|download_file"} ({$file.size})
			</a>
		</footer>
	</section>
{if isset($comments)}
{$comments}
{/if}
</article>