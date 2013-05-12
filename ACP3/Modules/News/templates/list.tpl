<div class="navbar navbar-inverse">
	<div class="navbar-inner">
{if isset($categories)}
{if {has_permission mod="newsletter" file="list"}}
		<div class="navbar-text pull-left">
			<h5><a href="{uri args="newsletter/list"}">{lang t="newsletter|list"}</a></h5>
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
	<header class="header">
		<small class="pull-right">
			<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
		</small>
		<h1>{$row.title}</h1>
	</header>
	<div class="content">
		{$row.text}
	</div>
{if $row.allow_comments}
	<footer class="align-center">
		<a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
		<span>({$row.comments})</span>
	</footer>
{/if}
</article>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}