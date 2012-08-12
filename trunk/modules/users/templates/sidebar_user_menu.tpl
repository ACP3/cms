<script type="text/javascript">
$(document).ready(function() {
	$('ul.admin > li:has(ul) > a').click(function() {
		$(this).next('ul').stop(true, true).slideToggle('slow');
		return false;
	});
});
</script>
<ul class="nav nav-list">
	<li class="nav-header">{lang t="users|user_menu"}</li>
	<li><a href="{uri args="users/home"}">{lang t="users|home"}</a></li>
{if isset($user_sidebar.modules)}
	<li class="dropdown">
		<a href="{uri args="acp"}" class="dropdown-toggle" data-toggle="dropdown" data-target="#">{lang t="users|administration"}</a>
		<ul class="dropdown-menu">
{foreach $user_sidebar.modules as $row}
			<li><a href="{uri args="acp/`$row.dir`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
{if isset($user_sidebar.system)}
	<li class="dropdown">
		<a href="{uri args="acp/system"}" class="dropdown-toggle" data-toggle="dropdown" data-target="#">{lang t="system|system"}</a>
		<ul class="dropdown-menu">
{foreach $user_sidebar.system as $row}
			<li><a href="{uri args="acp/system/`$row.page`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
	<li><a href="{uri args="users/logout/last_`$user_sidebar.page`"}">{lang t="users|logout"}</a></li>
</ul>