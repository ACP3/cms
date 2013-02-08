{if $overlay == 1}
{js_libraries enable="fancybox"}
<script type="text/javascript">
$(document).ready(function() {
	$('#create-link').click(function(e) {
		if (e.which == 1) {
			$.fancybox.open({ href: $(this).attr('href') + 'layout_simple/', title: $(this).attr('title') }, {
				type: 'iframe',
				autoSize: true,
				padding: 0
			});
			e.preventDefault();
		}
	});
});
</script>
{/if}
<p class="align-center">
	<a href="{uri args="guestbook/create"}" id="create-link" title="{lang t="guestbook|create"}">{lang t="guestbook|create"}</a>
</p>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($guestbook)}
{$pagination}
{foreach $guestbook as $row}
<article id="gb-entry-{$row.id}" class="dataset-box clearfix" style="width: 65%">
	<header class="header">
		<small class="pull-right">
			<time datetime="{$row.date_iso}">{$row.date_formatted}</time>
		</small>
		{if !empty($row.user_id)}<a href="{uri args="users/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>{else}{$row.name}{/if}<br>
	</header>
	<div class="content">
		<div class="pull-right">
{if $row.website != ''}
			<a href="{$row.website}" rel="nofollow" onclick="window.open(this.href); return false" title="{lang t="guestbook|visit_website"}">{icon path="16/gohome" width="16" height="16" alt="`$row.website`"}</a><br>
{/if}
{if $row.mail != ''}
			{mailto address=$row.mail encode="javascript" text="{icon path="16/mail" width="16" height="16" alt="`$row.mail`"}"}
{/if}
		</div>
		{$row.message}
	</div>
</article>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="system|no_entries"}</strong>
</div>
{/if}