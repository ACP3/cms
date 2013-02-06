<article>
	<section class="dataset-box">
		<header class="header">
			<small class="pull-right">
				<time datetime="{$news.date_iso}">{$news.date_formatted}</time>
			</small>
			<h1>{$news.title}</h1>
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