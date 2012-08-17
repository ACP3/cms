{if $overlay == 1}
{js_libraries enable="fancybox"}
<script type="text/javascript">
$(document).ready(function() {
	$('#create-link').click(function(e) {
		if (e.which == 1) {
			$.fancybox.open({ href: $(this).attr('href') + 'layout_simple/', title: $(this).attr('title') }, {
				type: 'iframe',
				autoSize: true,
				padding: 0,
				afterClose: function() {
					location.reload();
					return;
				}
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
{if isset($guestbook)}
{$pagination}
{foreach $guestbook as $row}
<div id="gb-entry-{$row.id}" class="dataset-box" style="width: 65%">
	<div class="header">
		<div class="f-right small">{$row.date}</div>
		{if !empty($row.user_id)}<a href="{uri args="users/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>{else}{$row.name}{/if}<br>
	</div>
	<div class="content">
		<div class="f-right">
{if $row.website != ''}
			<a href="{$row.website}" onclick="window.open(this.href); return false" title="{lang t="guestbook|visit_website"}">{icon path="16/gohome" width="16" height="16" alt="`$row.website`"}</a><br>
{/if}
{if $row.mail != ''}
			<a href="mailto:{$row.mail}" title="{lang t="guestbook|send_email"}">{icon path="16/mail" width="16" height="16" alt="`$row.mail`"}</a>
{/if}
		</div>
		{$row.message}
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}