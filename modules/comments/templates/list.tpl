<section id="comments">
	<header>
		<h1 class="align-center">{lang t="comments|comments"}</h1>
	</header>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($comments)}
{$pagination}
{foreach $comments as $row}
	<article class="dataset-box" style="width:65%">
		<div class="header">
			<small class="pull-right">
				<time date="{$row.date_iso}">{$row.date_formatted}</time>
			</small>
			{if !empty($row.user_id)}<a href="{uri args="users/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>{else}{$row.name}{/if}
		</div>
		<div class="content">
			{$row.message}
		</div>
	</article>
{/foreach}
{else}
	<div class="alert align-center">
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}
{if isset($comments_create_form)}
{$comments_create_form}
{/if}
</section>