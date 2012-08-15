<div class="dataset-box">
	<div class="header bigger">
{if isset($categories)}
{if ACP3_Modules::check('newsletter', 'create')}
		<div class="f-left">
			<a href="{uri args="newsletter/create"}">{lang t="newsletter|create"}</a>
		</div>
{/if}
		<div class="align-right">
			<form action="{uri args="news/list"}" method="post" class="form-inline">
				{$categories}
				<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
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
		<div class="f-right small">
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
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}