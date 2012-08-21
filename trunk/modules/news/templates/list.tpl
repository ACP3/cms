<div class="dataset-box">
	<div class="header bigger">
{if isset($categories)}
{if ACP3_Modules::check('newsletter', 'list')}
		<div class="pull-left">
			<a href="{uri args="newsletter/list"}">{lang t="newsletter|list"}</a>
		</div>
{/if}
		<div class="align-right">
			<form action="{uri args="news/list"}" method="post" class="form-inline">
				{$categories}
				<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
			</form>
		</div>
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
		{$row.headline}
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
	<strong>{lang t="common|no_entries"}</strong>
</div>
{/if}