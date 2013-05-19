<article>
	<section class="dataset-box">
		<header class="navbar">
			<div class="navbar-inner navbar-text">
				<small class="pull-right">
					<time datetime="{$file.date_iso}">{$file.date_formatted}</time>
				</small>
				<h2>{$file.title}</h2>
			</div>
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