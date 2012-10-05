<div class="navbar navbar-inverse">
	<div class="navbar-inner">
{if isset($categories)}
{if ACP3_Modules::check('newsletter', 'list')}
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
<div class="dataset-box">
	<div class="header">
		<div class="pull-right small">
			{$row.date}
		</div>
		{$row.title}
	</div>
	<div class="content">
		{$row.text}
{if $row.allow_comments}
		<p class="align-center">
			<a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
			<span>({$row.comments})</span>
		</p>
{/if}
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}