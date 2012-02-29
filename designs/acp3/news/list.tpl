{if modules::check('newsletter', 'create')}
<div style="text-align:center">
	<a href="{uri args="newsletter/create"}">{lang t="newsletter|create"}</a>
</div>
{/if}
{if isset($categories)}
<div class="news">
	<div class="header">
		<form action="{uri args="news/list"}" method="post" style="text-align:right">
			<div>
				<label for="cat" style="font-weight:bold">
					{lang t="common|category"}:
					{$categories}
				</label>
				<input type="submit" value="{lang t="common|submit"}" class="form">
			</div>
		</form>
	</div>
</div>
{/if}
{if isset($news)}
{$pagination}
{foreach $news as $row}
<div class="news">
	<h3 class="header">
		{$row.headline}
	</h3>
	<div class="date">
{if $row.allow_comments}
		<div class="comments">
			<a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
			<span>({$row.comments})</span>
		</div>
{/if}
		{$row.date}
	</div>
	<div class="content">
		{$row.text}
	</div>
</div>
{/foreach}
{else}
<div class="error-box">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}