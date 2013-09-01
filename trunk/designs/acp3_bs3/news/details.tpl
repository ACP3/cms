<article>
	<section class="dataset-box">
		<header class="navbar navbar-default">
			<div class="navbar-header">
				<h2 class="navbar-brand">{$news.title}</h2>
			</div>
			<small class="navbar-text pull-right">
				<time datetime="{$news.date_iso}">{$news.date_formatted}</time>
			</small>
		</header>
		<div class="content">
			{$news.text}
		</div>
		{if $news.uri != '' && $news.link_title != ''}
			<footer>
				<div class="hyperlink">
					<strong>{lang t="news|hyperlink"}:</strong> <a href="{$news.uri}"{$news.target}>{$news.link_title}</a>
				</div>
			</footer>
		{/if}
	</section>
	{if isset($comments)}
		{$comments}
	{/if}
</article>