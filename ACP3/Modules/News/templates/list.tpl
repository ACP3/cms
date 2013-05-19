<div class="navbar">
	<div class="navbar-inner">
{if isset($categories)}
{if {has_permission mod="newsletter" file="list"}}
		<div class="navbar-text pull-left">
			<a href="{uri args="newsletter/list"}">{lang t="newsletter|list"}</a>
		</div>
{/if}
		<form action="{uri args="news/list"}" method="post" class="navbar-form pull-right">
			{$categories}
			<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		</form>
{/if}
	</div>
</div>
{if isset($news)}
{$pagination}
{foreach $news as $row}
<article class="dataset-box">
	<header class="navbar">
		<div class="navbar-inner navbar-text">
			<small class="pull-right">
				<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
			</small>
			<h2><a href="{uri args="news/details/id_`$row.id`"}">{$row.title}</a></h2>
		</div>
	</header>
	<div class="content">
		{$row.text}
	</div>
{if isset($row.comments_count)}
	<footer class="align-center">
		<a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
		<span>({$row.comments_count})</span>
	</footer>
{/if}
</article>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}