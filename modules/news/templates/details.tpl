<div class="dataset-box">
	<div class="header">
		<div class="pull-right small">{$news.date}</div>
		{$news.headline}
	</div>
	<div class="content">
		{$news.text}
{if $news.uri != '' && $news.link_title != ''}
		<div class="hyperlink">
			<strong>{lang t="news|hyperlink"}:</strong> <a href="{$news.uri}"{$news.target}>{$news.link_title}</a>
		</div>
{/if}
	</div>
</div>
{if isset($comments)}
{$comments}
{/if}