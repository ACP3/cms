<h4>{lang t="users|user_menu"}</h4>
<script type="text/javascript">
$(document).ready(function() {
	$('ul.admin > li:has(ul) > a').click(function() {
		$(this).next('ul').stop(true, true).slideToggle('slow');
		return false;
	});
});
</script>
<ul class="admin">
	<li><a href="{uri args="users/home"}">{lang t="users|home"}</a></li>
{if isset($user_sidebar.modules)}
	<li>
		<a href="{uri args="acp"}">{lang t="users|administration"}</a>
		<ul>
{foreach $user_sidebar.modules as $row}
			<li><a href="{uri args="acp/`$row.dir`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
{if isset($user_sidebar.system)}
	<li>
		<a href="{uri args="acp/system"}">{lang t="system|system"}</a>
		<ul>
{foreach $user_sidebar.system as $row}
			<li><a href="{uri args="acp/system/`$row.page`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
	<li><a href="{uri args="users/logout/last_`$user_sidebar.page`"}">{lang t="users|logout"}</a></li>
</ul>